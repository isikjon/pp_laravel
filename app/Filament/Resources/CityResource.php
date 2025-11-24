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
                
                Tables\Actions\Action::make('deployConfig')
                    ->label('Деплой')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Задеплоить конфиг?')
                    ->modalDescription('Создаст и скопирует nginx конфиг на сервер')
                    ->action(function ($record) {
                        try {
                            $domain = config('app.domain', 'prostitutkimoskvytake.org');
                            $subdomain = $record->subdomain ? "{$record->subdomain}.{$domain}" : $domain;
                            
                            \Log::info("=== ДЕПЛОЙ СТАРТ ===", [
                                'city' => $record->name,
                                'code' => $record->code,
                                'subdomain' => $subdomain,
                                'time' => now()->toDateTimeString()
                            ]);
                            
                            // 1. Генерируем конфиг
                            \Log::info("Генерация конфига...");
                            self::generateNginxConfig($record);
                            
                            $configPath = storage_path("nginx/{$subdomain}.conf");
                            $configExists = file_exists($configPath);
                            $configSize = $configExists ? filesize($configPath) : 0;
                            
                            \Log::info("Конфиг создан", [
                                'path' => $configPath,
                                'exists' => $configExists,
                                'size' => $configSize
                            ]);
                            
                            // 2. Деплоим через API
                            $deployToken = config('app.deploy_token', 'your-secret-deploy-token-here');
                            
                            \Log::info("Вызов API деплоя...", ['subdomain' => $subdomain]);
                            
                            $response = \Http::timeout(120)->post(url('/api/deploy-config'), [
                                'subdomain' => $subdomain,
                                'token' => $deployToken
                            ]);
                            
                            if ($response->successful()) {
                                $data = $response->json();
                                
                                if ($data['success']) {
                                    Notification::make()
                                        ->success()
                                        ->title('Деплой выполнен')
                                        ->body('✓ Nginx: OK')
                                        ->duration(5000)
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->warning()
                                        ->title('Деплой выполнен с предупреждениями')
                                        ->body($data['message'] ?? 'Проверьте логи')
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Ошибка деплоя')
                                    ->body('HTTP ' . $response->status() . ': ' . $response->body())
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Ошибка')
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn ($record) => $record->subdomain),
                
                Tables\Actions\Action::make('setupSSL')
                    ->label('SSL')
                    ->icon('heroicon-o-lock-closed')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Установить SSL сертификат?')
                    ->modalDescription('Убедитесь что DNS настроен! A-запись должна указывать на IP сервера.')
                    ->action(function ($record) {
                        try {
                            $domain = config('app.domain', 'prostitutkimoskvytake.org');
                            $subdomain = $record->subdomain ? "{$record->subdomain}.{$domain}" : $domain;
                            $deployToken = config('app.deploy_token', 'your-secret-deploy-token-here');
                            
                            \Log::info("=== SSL УСТАНОВКА ===", [
                                'city' => $record->name,
                                'subdomain' => $subdomain,
                                'time' => now()->toDateTimeString()
                            ]);
                            
                            $response = \Http::timeout(120)->post(url('/api/setup-ssl'), [
                                'subdomain' => $subdomain,
                                'token' => $deployToken
                            ]);
                            
                            if ($response->successful()) {
                                $data = $response->json();
                                
                                if ($data['success']) {
                                    Notification::make()
                                        ->success()
                                        ->title('SSL установлен')
                                        ->body($data['message'] ?? '✓ Сертификат установлен')
                                        ->duration(5000)
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->warning()
                                        ->title('SSL не установлен')
                                        ->body($data['message'] ?? 'Проверьте DNS настройки')
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Ошибка SSL')
                                    ->body('HTTP ' . $response->status() . ': ' . $response->body())
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Ошибка')
                                ->body($e->getMessage())
                                ->send();
                        }
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
                            ->required()
                            ->reactive(),
                        
                        Forms\Components\Select::make('table_type')
                            ->label('Тип таблицы')
                            ->options([
                                'girls' => 'Девушки (girls)',
                                'masseuses' => 'Массажистки (masseuses)',
                            ])
                            ->default('girls')
                            ->required()
                            ->reactive(),
                        
                        Forms\Components\TextInput::make('limit')
                            ->label('Количество анкет')
                            ->helperText('Оставьте пустым, чтобы скопировать все. Укажите число для копирования первых N анкет (например: 500, 1500, 2000)')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Все'),
                    ])
                    ->action(function ($record, array $data) {
                        $count = self::syncData(
                            $data['source_city'], 
                            $record->code, 
                            $data['table_type'],
                            $data['limit'] ?? null
                        );
                        
                        $limitText = isset($data['limit']) ? " ({$data['limit']} анкет)" : '';
                        
                        Notification::make()
                            ->success()
                            ->title('Данные скопированы')
                            ->body("Скопировано {$count} анкет из {$data['source_city']} в {$record->code}{$limitText}")
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
    }
    
    protected static function syncData($fromCity, $toCity, $tableType = 'girls', $limit = null)
    {
        $fromTable = "{$tableType}_{$fromCity}";
        $toTable = "{$tableType}_{$toCity}";
        
        if (!Schema::hasTable($fromTable) || !Schema::hasTable($toTable)) {
            throw new \Exception("Таблица {$fromTable} или {$toTable} не существует");
        }
        
        $fromColumns = self::getTableColumns($fromTable);
        $toColumns = self::getTableColumns($toTable);
        $commonColumns = array_intersect($fromColumns, $toColumns);
        
        if (empty($commonColumns)) {
            throw new \Exception("Нет общих столбцов между таблицами");
        }
        
        $toCityRecord = City::where('code', $toCity)->first();
        $toCityName = $toCityRecord ? $toCityRecord->name : $toCity;
        
        if ($limit === null) {
            DB::table($toTable)->delete();
        }
        
        $query = DB::table($fromTable)->orderBy('id');
        
        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }
        
        $totalCopied = 0;
        
        $query->chunk(100, function($records) use ($toTable, $commonColumns, $toCityName, &$totalCopied) {
            foreach ($records as $record) {
                $data = [];
                foreach ($commonColumns as $column) {
                    if ($column === 'id') {
                        continue;
                    }
                    if ($column === 'city') {
                        $data[$column] = $toCityName;
                    } else {
                        $data[$column] = $record->$column ?? null;
                    }
                }
                
                if (!empty($data)) {
                    DB::table($toTable)->insert($data);
                    $totalCopied++;
                }
            }
        });
        
        $toRecord = City::where('code', $toCity)->first();
        if ($toRecord) {
            self::updateCounts($toRecord);
        }
        
        return $totalCopied;
    }
    
    protected static function getTableColumns($tableName)
    {
        try {
            $result = DB::select("PRAGMA table_info({$tableName})");
            $columns = [];
            foreach ($result as $row) {
                $columns[] = $row->name;
            }
            return $columns;
        } catch (\Exception $e) {
            try {
                return Schema::getColumnListing($tableName);
            } catch (\Exception $e2) {
                return [];
            }
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

