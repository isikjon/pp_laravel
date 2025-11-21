<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    
    protected static ?string $navigationLabel = 'Города';
    
    protected static ?string $modelLabel = 'Город';
    
    protected static ?string $pluralModelLabel = 'Города';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Код города')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z]+$/')
                            ->helperText('Только латиница, маленькие буквы (moscow, spb, ekb)')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('name')
                            ->label('Название города')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('subdomain')
                            ->label('Поддомен')
                            ->helperText('Оставьте пустым для основного домена')
                            ->maxLength(50)
                            ->regex('/^[a-z0-9-]*$/')
                            ->validationMessages([
                                'regex' => 'Только маленькие буквы, цифры и дефис. Без точек!'
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Убираем точки автоматически
                                $cleaned = preg_replace('/[^a-z0-9-]/', '', strtolower($state ?? ''));
                                $set('subdomain', $cleaned);
                            }),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subdomain')
                    ->label('Поддомен')
                    ->default('основной домен')
                    ->formatStateUsing(fn ($state) => $state ?: '—'),

                Tables\Columns\TextColumn::make('girls_count')
                    ->label('Девушки')
                    ->sortable(),

                Tables\Columns\TextColumn::make('masseuses_count')
                    ->label('Массажистки')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Активен'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активные'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('createTables')
                    ->label('Создать таблицы')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Создать таблицы БД')
                    ->modalDescription(fn ($record) => "Создать таблицы girls_{$record->code} и masseuses_{$record->code}?")
                    ->action(function ($record) {
                        self::createDatabaseTables($record);
                        
                        Notification::make()
                            ->success()
                            ->title('Таблицы созданы')
                            ->body("Таблицы для города {$record->name} успешно созданы")
                            ->send();
                    })
                    ->visible(fn ($record) => !self::tablesExist($record)),
                
                Tables\Actions\Action::make('generateConfig')
                    ->label('Nginx конфиг')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->action(function ($record) {
                        self::generateNginxConfig($record);
                        
                        Notification::make()
                            ->success()
                            ->title('Конфиг создан')
                            ->body("Конфиг создан в storage/nginx/{$record->subdomain}.prostitutkimoskvytake.org.conf")
                            ->send();
                    })
                    ->visible(fn ($record) => $record->subdomain),
                
                Tables\Actions\Action::make('syncFrom')
                    ->label('Синхронизировать')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('source_city')
                            ->label('Откуда копировать данные')
                            ->options(function ($record) {
                                return City::where('id', '!=', $record->id)
                                    ->pluck('name', 'code');
                            })
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        self::syncData($data['source_city'], $record->code);
                        
                        Notification::make()
                            ->success()
                            ->title('Данные скопированы')
                            ->body("Данные из {$data['source_city']} скопированы в {$record->code}")
                            ->send();
                    }),
                
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Удалить город')
                    ->modalDescription('Таблицы БД не будут удалены, только запись о городе'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
    
    protected static function tablesExist($record): bool
    {
        return Schema::hasTable("girls_{$record->code}") && 
               Schema::hasTable("masseuses_{$record->code}");
    }
    
    protected static function createDatabaseTables($record)
    {
        $tables = [
            "girls_{$record->code}" => self::getGirlsTableStructure(),
            "masseuses_{$record->code}" => self::getMasseusesTableStructure(),
        ];
        
        foreach ($tables as $tableName => $structure) {
            if (!Schema::hasTable($tableName)) {
                DB::statement($structure($tableName));
            }
        }
        
        self::updateCounts($record);
    }
    
    protected static function getGirlsTableStructure()
    {
        return function($tableName) {
            return "CREATE TABLE `{$tableName}` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `anketa_id` TEXT NOT NULL UNIQUE,
                `name` TEXT NOT NULL,
                `age` TEXT,
                `height` INTEGER,
                `weight` INTEGER,
                `breast_size` INTEGER,
                `metro` TEXT,
                `district` TEXT,
                `city` TEXT,
                `phone` TEXT,
                `whatsapp` TEXT,
                `telegram` TEXT,
                `coordinates` TEXT,
                `services` TEXT,
                `tariffs` TEXT,
                `meeting_places` TEXT,
                `media_images` TEXT,
                `about` TEXT,
                `hair_color` TEXT,
                `pubic_hair` TEXT,
                `ethnicity` TEXT,
                `rating` REAL DEFAULT 0,
                `views_count` INTEGER DEFAULT 0,
                `is_verified` INTEGER DEFAULT 0,
                `is_premium` INTEGER DEFAULT 0,
                `sort_order` INTEGER DEFAULT 0,
                `created_at` TEXT,
                `updated_at` TEXT
            )";
        };
    }
    
    protected static function getMasseusesTableStructure()
    {
        return function($tableName) {
            return "CREATE TABLE `{$tableName}` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `anketa_id` TEXT NOT NULL UNIQUE,
                `name` TEXT NOT NULL,
                `age` TEXT,
                `height` INTEGER,
                `weight` INTEGER,
                `breast_size` INTEGER,
                `metro` TEXT,
                `district` TEXT,
                `city` TEXT,
                `phone` TEXT,
                `whatsapp` TEXT,
                `telegram` TEXT,
                `coordinates` TEXT,
                `services` TEXT,
                `tariffs` TEXT,
                `meeting_places` TEXT,
                `media_images` TEXT,
                `about` TEXT,
                `massage_types` TEXT,
                `rating` REAL DEFAULT 0,
                `views_count` INTEGER DEFAULT 0,
                `is_verified` INTEGER DEFAULT 0,
                `is_premium` INTEGER DEFAULT 0,
                `sort_order` INTEGER DEFAULT 0,
                `created_at` TEXT,
                `updated_at` TEXT
            )";
        };
    }
    
    protected static function generateNginxConfig($record)
    {
        $domain = config('app.domain', 'prostitutkimoskvytake.org');
        $subdomain = $record->subdomain ? "{$record->subdomain}.{$domain}" : $domain;
        $projectPath = env('PROJECT_PATH', '/var/www/noviysayt/data/www/prostitutkimoskvytake.org');
        $serverIp = env('SERVER_IP', '45.82.66.116');
        $phpFpmSock = env('PHP_FPM_SOCK', '/var/www/php-fpm/3584.sock');
        $sslCert = env('SSL_CERT', '/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.crtca');
        $sslKey = env('SSL_KEY', '/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.key');
        
        $config = view('nginx.config', compact(
            'subdomain', 'projectPath', 'serverIp', 'phpFpmSock', 'sslCert', 'sslKey'
        ))->render();
        
        $configDir = storage_path('nginx');
        if (!File::exists($configDir)) {
            File::makeDirectory($configDir, 0755, true);
        }
        
        File::put("{$configDir}/{$subdomain}.conf", $config);
        
        // Автоматически деплоим конфиг на сервер
        exec('sudo /usr/local/bin/deploy-nginx-config 2>&1', $deployOutput, $deployReturn);
        
        // Автоматически настраиваем SSL если нужно
        if ($record->subdomain) {
            exec("sudo /usr/local/bin/setup-ssl-for-subdomain {$subdomain} 2>&1", $sslOutput, $sslReturn);
        }
    }
    
    protected static function syncData($fromCity, $toCity)
    {
        $tables = ['girls', 'masseuses'];
        
        foreach ($tables as $baseTable) {
            $fromTable = "{$baseTable}_{$fromCity}";
            $toTable = "{$baseTable}_{$toCity}";
            
            if (!Schema::hasTable($fromTable) || !Schema::hasTable($toTable)) {
                continue;
            }
            
            DB::table($toTable)->truncate();
            
            DB::table($fromTable)->orderBy('id')->chunk(100, function($records) use ($toTable) {
                foreach ($records as $record) {
                    $data = (array) $record;
                    unset($data['id']);
                    DB::table($toTable)->insert($data);
                }
            });
        }
        
        $toRecord = City::where('code', $toCity)->first();
        if ($toRecord) {
            self::updateCounts($toRecord);
        }
    }
    
    protected static function updateCounts($record)
    {
        $girlsCount = Schema::hasTable("girls_{$record->code}") 
            ? DB::table("girls_{$record->code}")->count() 
            : 0;
            
        $masseusesCount = Schema::hasTable("masseuses_{$record->code}") 
            ? DB::table("masseuses_{$record->code}")->count() 
            : 0;
        
        $record->update([
            'girls_count' => $girlsCount,
            'masseuses_count' => $masseusesCount,
        ]);
    }
}

