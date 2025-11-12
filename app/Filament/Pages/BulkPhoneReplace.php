<?php

namespace App\Filament\Pages;

use App\Models\Girl;
use App\Models\Masseuse;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;

class BulkPhoneReplace extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static string $view = 'filament.pages.bulk-phone-replace';
    
    protected static ?string $navigationLabel = 'Массовая замена номеров';
    
    protected static ?string $title = 'Массовая замена номеров';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $navigationGroup = 'Инструменты';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }
    
    #[Computed]
    protected function profilesList(): array
    {
        $city = $this->data['city'] ?? 'Москва';
        $resourceType = $this->data['resource_type'] ?? 'girls';
        
        if (empty($city) || empty($resourceType)) {
            return [];
        }
        
        $tableName = $this->getTableName($resourceType, $city);
        
        if (empty($tableName)) {
            return [];
        }
        
        try {
            $anketaIdField = $this->getAnketaIdField($resourceType);
            $query = DB::table($tableName)
                ->select('id', 'name', $anketaIdField, 'sort_order', 'city', 'metro')
                ->limit(500);
            
            if (Schema::hasColumn($tableName, 'sort_order')) {
                $query->orderBy('sort_order', 'asc');
            }
            $query->orderBy('id', 'asc');
            
            $profiles = $query->get();
            
            return $profiles->map(function($profile) use ($anketaIdField) {
                return [
                    'id' => $profile->id,
                    'anketa_id' => $profile->$anketaIdField ?? null,
                    'name' => $profile->name ?? 'Без имени',
                    'sort_order' => $profile->sort_order ?? 999999,
                    'city' => $profile->city ?? null,
                    'metro' => $profile->metro ?? null,
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    protected function getTableName(string $resourceType, string $city): string
    {
        $cityCode = $city === 'Москва' ? 'moscow' : 'spb';
        
        return match($resourceType) {
            'girls' => $cityCode === 'moscow' ? 'girls_moscow' : 'girls_spb',
            'masseuses' => $cityCode === 'moscow' ? 'masseuses_moscow' : 'masseuses_spb',
            'salons' => 'salons',
            'strip_clubs' => 'strip_clubs',
            default => '',
        };
    }
    
    protected function getIdField(string $resourceType): string
    {
        return match($resourceType) {
            'girls' => 'anketa_id',
            'masseuses' => 'anketa_id',
            'salons' => 'salon_id',
            'strip_clubs' => 'club_id',
            default => 'id',
        };
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Выбор диапазона')
                    ->schema([
                        Select::make('city')
                            ->label('Город')
                            ->options([
                                'Москва' => 'Москва',
                                'Санкт-Петербург' => 'Санкт-Петербург',
                            ])
                            ->required()
                            ->default('Москва')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('resource_type', null);
                                $set('range_type', null);
                                $set('from_id', null);
                                $set('to_id', null);
                                unset($this->profilesList);
                                $this->dispatch('$refresh');
                            })
                            ->helperText('Выберите город для работы'),
                        
                        Select::make('resource_type')
                            ->label('Тип ресурса')
                            ->options([
                                'girls' => 'Girls (Анкеты девушек)',
                                'masseuses' => 'Masseuses (Массажистки)',
                                'salons' => 'Salons (Интим-салоны)',
                                'strip_clubs' => 'Strip Clubs (Стрип-клубы)',
                            ])
                            ->required()
                            ->default('girls')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('range_type', null);
                                $set('from_id', null);
                                $set('to_id', null);
                                unset($this->profilesList);
                                $this->dispatch('$refresh');
                            }),
                        
                        Select::make('range_type')
                            ->label('Диапазон')
                            ->options([
                                'all' => 'Все записи',
                                'first_500' => 'Первые 500',
                                'first_1000' => 'Первые 1000',
                                'custom' => 'Произвольный диапазон (от X до Y)',
                            ])
                            ->required()
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => [
                                $set('from_id', null),
                                $set('to_id', null),
                            ]),
                        
                        Select::make('from_id')
                            ->label('От записи')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, Get $get) {
                                $resourceType = $get('resource_type') ?? 'girls';
                                return $this->getRecordsForSelect($resourceType, $search);
                            })
                            ->getOptionLabelUsing(function ($value, Get $get) {
                                $resourceType = $get('resource_type') ?? 'girls';
                                return $this->getRecordLabel($resourceType, $value);
                            })
                            ->visible(fn (Get $get): bool => $get('range_type') === 'custom')
                            ->required(fn (Get $get): bool => $get('range_type') === 'custom')
                            ->helperText('Начните вводить ID или имя для поиска'),
                        
                        Select::make('to_id')
                            ->label('До записи')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, Get $get) {
                                $resourceType = $get('resource_type') ?? 'girls';
                                return $this->getRecordsForSelect($resourceType, $search);
                            })
                            ->getOptionLabelUsing(function ($value, Get $get) {
                                $resourceType = $get('resource_type') ?? 'girls';
                                return $this->getRecordLabel($resourceType, $value);
                            })
                            ->visible(fn (Get $get): bool => $get('range_type') === 'custom')
                            ->required(fn (Get $get): bool => $get('range_type') === 'custom')
                            ->helperText('Начните вводить ID или имя для поиска'),
                    ])->columns(1),
                
                Section::make('Новый номер телефона')
                    ->schema([
                        TextInput::make('new_phone')
                            ->label('Новый номер')
                            ->mask('+7(999)999-99-99')
                            ->placeholder('+7(999)999-99-99')
                            ->required()
                            ->helperText('Этот номер будет установлен для всех записей в выбранном диапазоне'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getRecordsForSelect(string $resourceType, string $search = ''): array
    {
        $city = $this->data['city'] ?? 'Москва';
        $tableName = $this->getTableName($resourceType, $city);
        $anketaIdField = $this->getAnketaIdField($resourceType);
        
        if (empty($tableName)) {
            return [];
        }
        
        $query = DB::table($tableName)
            ->select('id', 'name', $anketaIdField, 'sort_order');
        
        if (Schema::hasColumn($tableName, 'sort_order')) {
            $query->orderBy('sort_order', 'asc');
        }
        $query->orderBy('id', 'asc');
        
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $anketaIdField) {
                $q->where($anketaIdField, 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        $query->limit(100);
        
        $profiles = $query->get();
        $profilesList = $this->profilesList;
        
        return $profiles->mapWithKeys(function ($record) use ($anketaIdField, $profilesList) {
            // Находим позицию в списке
            $position = 999999;
            foreach ($profilesList as $index => $profile) {
                if ($profile['id'] == $record->id) {
                    $position = $index + 1;
                    break;
                }
            }
            
            $anketaId = $record->$anketaIdField ?? 'N/A';
            $sortOrder = $record->sort_order ?? 999999;
            
            return [
                $record->id => "#{$position} | ID: {$record->id} | {$anketaIdField}: {$anketaId} | {$record->name} | Позиция: {$sortOrder}"
            ];
        })->toArray();
    }

    protected function getRecordLabel(string $resourceType, $id): string
    {
        $city = $this->data['city'] ?? 'Москва';
        $tableName = $this->getTableName($resourceType, $city);
        $anketaIdField = $this->getAnketaIdField($resourceType);
        
        if (empty($tableName)) {
            return "ID: {$id}";
        }
        
        $record = DB::table($tableName)->where('id', $id)->first();
        
        if (!$record) {
            return "ID: {$id}";
        }
        
        $profilesList = $this->profilesList;
        $position = 999999;
        foreach ($profilesList as $index => $profile) {
            if ($profile['id'] == $id) {
                $position = $index + 1;
                break;
            }
        }
        
        $anketaId = $record->$anketaIdField ?? 'N/A';
        $sortOrder = $record->sort_order ?? 999999;
        
        return "#{$position} | ID: {$id} | {$anketaIdField}: {$anketaId} | {$record->name} | Позиция: {$sortOrder}";
    }

    protected function getModelClass(string $resourceType): string
    {
        return match ($resourceType) {
            'girls' => \App\Models\Girl::class,
            'masseuses' => \App\Models\Masseuse::class,
            'salons' => \App\Models\Salon::class,
            'strip_clubs' => \App\Models\StripClub::class,
            default => \App\Models\Girl::class,
        };
    }

    protected function getAnketaIdField(string $resourceType): string
    {
        return match ($resourceType) {
            'girls' => 'anketa_id',
            'masseuses' => 'anketa_id',
            'salons' => 'salon_id',
            'strip_clubs' => 'club_id',
            default => 'anketa_id',
        };
    }

    public function replacePhones(): void
    {
        $data = $this->form->getState();
        
        $city = $data['city'];
        $resourceType = $data['resource_type'];
        $rangeType = $data['range_type'];
        $newPhone = $data['new_phone'];
        
        $tableName = $this->getTableName($resourceType, $city);
        
        if (empty($tableName)) {
            Notification::make()
                ->danger()
                ->title('Ошибка!')
                ->body('Неверный тип ресурса или город')
                ->send();
            return;
        }
        
        try {
            DB::beginTransaction();
            
            $query = DB::table($tableName);
            
            // Определяем диапазон
            switch ($rangeType) {
                case 'all':
                    // Все записи - ничего не делаем с query
                    break;
                    
                case 'first_500':
                    $query->orderBy('id', 'asc')->limit(500);
                    break;
                    
                case 'first_1000':
                    $query->orderBy('id', 'asc')->limit(1000);
                    break;
                    
                case 'custom':
                    $fromId = $data['from_id'];
                    $toId = $data['to_id'];
                    
                    if ($fromId > $toId) {
                        throw new \Exception('ID начала диапазона не может быть больше ID конца диапазона');
                    }
                    
                    $query->whereBetween('id', [$fromId, $toId]);
                    break;
            }
            
            // Получаем ID записей для обновления
            $recordIds = $query->pluck('id')->toArray();
            $count = count($recordIds);
            
            if ($count === 0) {
                Notification::make()
                    ->warning()
                    ->title('Записи не найдены')
                    ->body('В выбранном диапазоне нет записей для обновления.')
                    ->send();
                
                DB::rollBack();
                return;
            }
            
            // Выполняем массовое обновление
            DB::table($tableName)->whereIn('id', $recordIds)->update(['phone' => $newPhone]);
            
            // Сбрасываем кэш Computed property
            unset($this->profilesList);
            
            DB::commit();
            
            Notification::make()
                ->success()
                ->title('Успешно!')
                ->body("Номер обновлен для {$count} записей в городе: {$city}.")
                ->send();
            
            // Очищаем форму
            $this->form->fill();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->danger()
                ->title('Ошибка!')
                ->body('Не удалось обновить номера: ' . $e->getMessage())
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('replace')
                ->label('Заменить номера')
                ->icon('heroicon-o-phone-arrow-down-left')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Подтвердите массовую замену')
                ->modalDescription(function () {
                    $data = $this->form->getState();
                    $city = $data['city'] ?? 'Москва';
                    $rangeType = $data['range_type'] ?? 'all';
                    
                    $message = match ($rangeType) {
                        'all' => 'Заменить номер для ВСЕХ записей.',
                        'first_500' => 'Заменить номер для первых 500 записей.',
                        'first_1000' => 'Заменить номер для первых 1000 записей.',
                        'custom' => 'Заменить номер для записей в выбранном диапазоне.',
                        default => 'Массовая замена номеров.',
                    };
                    
                    return $message . "\n\nГород: {$city}\n\nЭто действие нельзя отменить!";
                })
                ->modalSubmitActionLabel('Да, заменить')
                ->modalCancelActionLabel('Отмена')
                ->action('replacePhones')
                ->submit('replace'),
        ];
    }
}

