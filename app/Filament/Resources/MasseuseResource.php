<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasseuseResource\Pages;
use App\Models\Masseuse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MasseuseResource extends Resource
{
    protected static ?string $model = Masseuse::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    
    protected static ?string $navigationLabel = 'Masseuses';
    
    protected static ?string $modelLabel = 'Masseuse';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('anketa_id')
                            ->label('Anketa ID')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('title')
                            ->label('Title'),
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        Forms\Components\TextInput::make('age')
                            ->label('Age')
                            ->numeric()
                            ->minValue(18),
                    ])->columns(2),
                
                Forms\Components\Section::make('Physical Parameters')
                    ->schema([
                        Forms\Components\TextInput::make('height')
                            ->numeric()
                            ->label('Height (cm)'),
                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->label('Weight (kg)'),
                        Forms\Components\TextInput::make('bust')
                            ->numeric()
                            ->label('Bust size'),
                        Forms\Components\TextInput::make('hair_color')
                            ->label('Hair Color'),
                        Forms\Components\TextInput::make('nationality')
                            ->label('Nationality'),
                        Forms\Components\TextInput::make('intimate_trim')
                            ->label('Intimate Trim'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->mask('+7(999)999-99-99')
                            ->placeholder('+7(999)999-99-99')
                            ->required(),
                        Forms\Components\TextInput::make('call_availability')
                            ->label('Call Availability'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('city')
                            ->label('City'),
                        Forms\Components\TextInput::make('metro')
                            ->label('Metro'),
                        Forms\Components\TextInput::make('district')
                            ->label('District'),
                        Forms\Components\TextInput::make('map_link')
                            ->label('Map Link')
                            ->url()
                            ->columnSpanFull(),
                    ])->columns(3),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('meeting_places')
                            ->label('Meeting Places (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tariffs')
                            ->label('Tariffs (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('services')
                            ->label('Services (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('media_images')
                            ->label('Media Images (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('media_video')
                            ->label('Media Video URL')
                            ->url()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('original_url')
                            ->label('Original URL')
                            ->url()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('reviews_comments')
                            ->label('Reviews & Comments')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anketa_id')
                    ->label('Anketa ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable(),
                Tables\Columns\TextColumn::make('metro')
                    ->label('Metro')
                    ->searchable(),
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
                        $moscow = Masseuse::from('masseuses_moscow')->whereNotNull('city')->distinct()->pluck('city', 'city')->toArray();
                        $spb = Masseuse::from('masseuses_spb')->whereNotNull('city')->distinct()->pluck('city', 'city')->toArray();
                        return array_merge($moscow, $spb);
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
            'index' => Pages\ListMasseuses::route('/'),
            'create' => Pages\CreateMasseuse::route('/create'),
            'view' => Pages\ViewMasseuse::route('/{record}'),
            'edit' => Pages\EditMasseuse::route('/{record}/edit'),
        ];
    }
}

