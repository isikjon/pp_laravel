<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GirlResource\Pages;
use App\Models\Girl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GirlResource extends Resource
{
    protected static ?string $model = Girl::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Girls';
    
    protected static ?string $modelLabel = 'Girl';

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
                            ->label('Height (cm)')
                            ->placeholder('165'),
                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->label('Weight (kg)')
                            ->placeholder('55'),
                        Forms\Components\TextInput::make('bust')
                            ->numeric()
                            ->label('Bust size')
                            ->placeholder('2'),
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->label('City')
                    ->options(function () {
                        $cities = \App\Models\City::where('is_active', true)->get();
                        $allCities = [];
                        
                        foreach ($cities as $city) {
                            $tableName = "girls_{$city->code}";
                            if (\Schema::hasTable($tableName)) {
                                $cityOptions = Girl::from($tableName)
                                    ->whereNotNull('city')
                                    ->distinct()
                                    ->pluck('city', 'city')
                                    ->toArray();
                                $allCities = array_merge($allCities, $cityOptions);
                            }
                        }
                        
                        return $allCities;
                    }),
                Tables\Filters\SelectFilter::make('metro')
                    ->label('Metro')
                    ->options(function () {
                        $cities = \App\Models\City::where('is_active', true)->get();
                        $allMetros = [];
                        
                        foreach ($cities as $city) {
                            $tableName = "girls_{$city->code}";
                            if (\Schema::hasTable($tableName)) {
                                $metroOptions = Girl::from($tableName)
                                    ->whereNotNull('metro')
                                    ->distinct()
                                    ->pluck('metro', 'metro')
                                    ->toArray();
                                $allMetros = array_merge($allMetros, $metroOptions);
                            }
                        }
                        
                        return $allMetros;
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

    public static function getEloquentQuery(): Builder
    {
        $cities = \App\Models\City::where('is_active', true)->get();
        $unions = [];
        
        $commonColumns = [
            'id', 'anketa_id', 'name', 'age', 'city', 'metro', 'district',
            'phone', 'height', 'weight', 'hair_color', 'services', 'tariffs',
            'meeting_places', 'media_images', 'sort_order', 'created_at', 'updated_at'
        ];
        
        foreach ($cities as $city) {
            $tableName = "girls_{$city->code}";
            if (\Schema::hasTable($tableName)) {
                $unions[] = DB::table($tableName)->select($commonColumns);
            }
        }
        
        if (empty($unions)) {
            return Girl::query()->whereRaw('1 = 0');
        }
        
        $union = array_shift($unions);
        
        foreach ($unions as $unionQuery) {
            $union = $union->unionAll($unionQuery);
        }
        
        return Girl::fromSub($union, 'girls_combined');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGirls::route('/'),
            'create' => Pages\CreateGirl::route('/create'),
            'view' => Pages\ViewGirl::route('/{record}'),
            'edit' => Pages\EditGirl::route('/{record}/edit'),
        ];
    }
}

