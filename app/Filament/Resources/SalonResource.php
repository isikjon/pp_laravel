<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalonResource\Pages;
use App\Models\Salon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalonResource extends Resource
{
    protected static ?string $model = Salon::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Salons';
    
    protected static ?string $modelLabel = 'Salon';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('salon_id')
                            ->label('Salon ID')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url(),
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\Textarea::make('phones')
                            ->label('Phones (JSON)')
                            ->rows(2),
                        Forms\Components\TextInput::make('schedule')
                            ->label('Schedule'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('city')
                            ->label('City'),
                        Forms\Components\TextInput::make('metro')
                            ->label('Metro'),
                        Forms\Components\TextInput::make('district')
                            ->label('District'),
                        Forms\Components\TextInput::make('coordinates')
                            ->label('Coordinates'),
                        Forms\Components\TextInput::make('map_link')
                            ->label('Map Link')
                            ->url()
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tariffs')
                            ->label('Tariffs (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('images')
                            ->label('Images (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('girls')
                            ->label('Girls (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('reviews')
                            ->label('Reviews (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('salon_id')
                    ->label('Salon ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable(),
                Tables\Columns\TextColumn::make('metro')
                    ->label('Metro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule')
                    ->label('Schedule')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->label('City')
                    ->options(function () {
                        return Salon::query()
                            ->whereNotNull('city')
                            ->distinct()
                            ->pluck('city', 'city')
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalons::route('/'),
            'create' => Pages\CreateSalon::route('/create'),
            'view' => Pages\ViewSalon::route('/{record}'),
            'edit' => Pages\EditSalon::route('/{record}/edit'),
        ];
    }
}

