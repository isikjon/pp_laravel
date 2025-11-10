<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactRequestResource\Pages;
use App\Models\ContactRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->required()
                            ->disabled(),
                    ])->columns(3),
                
                Forms\Components\Section::make('Girl Information')
                    ->schema([
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
                            ->url()
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(3),
                
                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\TextInput::make('page_url')
                            ->label('Request Page URL')
                            ->url()
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('new'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('girl_name')
                    ->label('Girl Name')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('girl_phone')
                    ->label('Girl Phone')
                    ->searchable()
                    ->placeholder('—')
                    ->copyable(),
                Tables\Columns\TextColumn::make('girl_url')
                    ->label('Girl URL')
                    ->searchable()
                    ->placeholder('—')
                    ->limit(30)
                    ->url(fn ($record) => $record->girl_url, shouldOpenInNewTab: true)
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'new',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ]),
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
            'index' => Pages\ListContactRequests::route('/'),
            'create' => Pages\CreateContactRequest::route('/create'),
            'view' => Pages\ViewContactRequest::route('/{record}'),
            'edit' => Pages\EditContactRequest::route('/{record}/edit'),
        ];
    }
}

