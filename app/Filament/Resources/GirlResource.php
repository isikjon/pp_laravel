<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GirlResource\Pages;
use App\Filament\Resources\GirlResource\RelationManagers;
use App\Models\Girl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GirlResource extends Resource
{
    protected static ?string $model = Girl::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('anketa_id')
                    ->required(),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('age'),
                Forms\Components\TextInput::make('height')
                    ->numeric()
                    ->label('Рост (см)')
                    ->placeholder('165'),
                Forms\Components\TextInput::make('weight')
                    ->numeric()
                    ->label('Вес (кг)')
                    ->placeholder('55'),
                Forms\Components\TextInput::make('bust')
                    ->numeric()
                    ->label('Грудь (размер)')
                    ->placeholder('2'),
                Forms\Components\TextInput::make('phone')
                    ->mask('+7(999)999-99-99')
                    ->placeholder('+7(999)999-99-99')
                    ->required(),
                Forms\Components\TextInput::make('call_availability'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('metro'),
                Forms\Components\TextInput::make('district'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anketa_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('metro')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGirls::route('/'),
            'create' => Pages\CreateGirl::route('/create'),
            'edit' => Pages\EditGirl::route('/{record}/edit'),
        ];
    }
}
