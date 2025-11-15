<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageSettingsResource\Pages;
use App\Filament\Resources\HomePageSettingsResource\RelationManagers;
use App\Models\HomePageSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HomePageSettingsResource extends Resource
{
    protected static ?string $model = HomePageSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Главная страница';
    
    protected static ?string $modelLabel = 'Главная страница';
    
    protected static ?string $pluralModelLabel = 'Главная страница';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SEO Главной страницы')
                    ->schema([
                        Forms\Components\Select::make('city')
                            ->label('Город')
                            ->required()
                            ->options([
                                'moscow' => 'Москва',
                                'spb' => 'Санкт-Петербург',
                            ])
                            ->default('moscow')
                            ->helperText('Выберите город для настройки SEO'),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Title (Заголовок)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('ProstitutkiMoscow')
                            ->helperText('Отображается во вкладке браузера и в поисковой выдаче'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Meta Description (Описание)')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Каталог анкет с подробными фильтрами и проверенными предложениями')
                            ->helperText('Отображается в поисковой выдаче под заголовком. Максимум 500 символов.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city')
                    ->label('Город')
                    ->formatStateUsing(fn (string $state): string => $state === 'spb' ? 'Санкт-Петербург' : 'Москва')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'spb' ? 'success' : 'primary')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(100),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->label('Город')
                    ->options([
                        'moscow' => 'Москва',
                        'spb' => 'Санкт-Петербург',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHomePageSettings::route('/'),
        ];
    }
}
