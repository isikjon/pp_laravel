<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactRequestResource\Pages;
use App\Filament\Resources\ContactRequestResource\RelationManagers;
use App\Models\ContactRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactRequestResource extends Resource
{
    protected static ?string $model = ContactRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = 'Contact Requests';
    
    protected static ?string $modelLabel = 'Contact Request';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('girl_anketa_id')
                    ->label('Girl Anketa ID')
                    ->disabled(),
                Forms\Components\TextInput::make('girl_name')
                    ->label('Girl Name')
                    ->disabled(),
                Forms\Components\TextInput::make('girl_phone')
                    ->label('Girl Phone')
                    ->disabled(),
                Forms\Components\TextInput::make('girl_url')
                    ->label('Girl Page URL')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('page_url')
                    ->label('Request Page URL')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('girl_name')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('girl_phone')
                    ->searchable()
                    ->placeholder('—')
                    ->label('Girl Phone'),
                Tables\Columns\TextColumn::make('girl_url')
                    ->searchable()
                    ->placeholder('—')
                    ->label('Girl URL')
                    ->limit(30)
                    ->url(fn ($record) => $record->girl_url, shouldOpenInNewTab: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'new',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactRequests::route('/'),
            'create' => Pages\CreateContactRequest::route('/create'),
            'edit' => Pages\EditContactRequest::route('/{record}/edit'),
        ];
    }
}
