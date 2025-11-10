<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StripClubResource\Pages;
use App\Models\StripClub;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StripClubResource extends Resource
{
    protected static ?string $model = StripClub::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';
    
    protected static ?string $navigationLabel = 'Strip Clubs';
    
    protected static ?string $modelLabel = 'Strip Club';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('club_id')
                            ->label('Club ID')
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
                Tables\Columns\TextColumn::make('club_id')
                    ->label('Club ID')
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
                        return StripClub::query()
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
            'index' => Pages\ListStripClubs::route('/'),
            'create' => Pages\CreateStripClub::route('/create'),
            'view' => Pages\ViewStripClub::route('/{record}'),
            'edit' => Pages\EditStripClub::route('/{record}/edit'),
        ];
    }
}

