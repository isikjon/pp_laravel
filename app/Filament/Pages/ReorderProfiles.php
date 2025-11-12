<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Girl;
use App\Models\Masseuse;
use App\Models\Salon;
use App\Models\StripClub;
use Livewire\Attributes\Computed;

class ReorderProfiles extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';
    
    protected static string $view = 'filament.pages.reorder-profiles';
    
    protected static ?string $navigationLabel = 'Управление позициями';
    
    protected static ?string $title = 'Управление позициями анкет';
    
    protected static ?int $navigationSort = 10;
    
    public ?string $resourceType = null;
    public ?string $city = null;
    public ?int $selectedProfile = null;
    public ?int $newPosition = null;
    
    // Не сериализуем profilesList в snapshot чтобы не перегружать память
    // Используем Computed для ленивой загрузки
    #[Computed]
    protected function profilesList(): array
    {
        if (empty($this->resourceType) || empty($this->city)) {
            return [];
        }
        
        $tableName = $this->getTableName();
        
        if (empty($tableName)) {
            return [];
        }
        
        try {
            // Загружаем только необходимые поля чтобы не перегружать память
            $query = DB::table($tableName)
                ->select('id', 'anketa_id', 'name', 'sort_order', 'city', 'metro')
                ->limit(500); // Ограничиваем количество записей для экономии памяти
            
            // Проверяем наличие колонки sort_order
            if (Schema::hasColumn($tableName, 'sort_order')) {
                $query->orderBy('sort_order', 'asc');
            }
            $query->orderBy('id', 'asc');
            
            $profiles = $query->get();
            
            // Преобразуем stdClass в массив, оставляя только нужные поля
            return $profiles->map(function($profile) {
                return [
                    'id' => $profile->id,
                    'anketa_id' => $profile->anketa_id ?? null,
                    'name' => $profile->name ?? 'Без имени',
                    'sort_order' => $profile->sort_order ?? 999999,
                    'city' => $profile->city ?? null,
                    'metro' => $profile->metro ?? null,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('ReorderProfiles::profilesList error: ' . $e->getMessage());
            return [];
        }
    }
    
    public function mount(): void
    {
        Log::info('ReorderProfiles: mount() called', [
            'initial_resourceType' => $this->resourceType,
            'initial_city' => $this->city,
            'initial_selectedProfile' => $this->selectedProfile,
            'initial_newPosition' => $this->newPosition,
            'component_id' => $this->getId()
        ]);
        
        // Преобразуем значения в правильные типы
        if ($this->selectedProfile !== null && !is_int($this->selectedProfile)) {
            $this->selectedProfile = is_numeric($this->selectedProfile) ? (int) $this->selectedProfile : null;
        }
        if ($this->newPosition !== null && !is_int($this->newPosition)) {
            $this->newPosition = is_numeric($this->newPosition) ? (int) $this->newPosition : null;
        }
        
        $this->form->fill([
            'resourceType' => $this->resourceType,
            'city' => $this->city,
            'selectedProfile' => $this->selectedProfile,
            'newPosition' => $this->newPosition,
        ]);
        
        Log::info('ReorderProfiles: mount() - form filled', [
            'resourceType' => $this->resourceType,
            'city' => $this->city,
            'selectedProfile' => $this->selectedProfile,
            'selectedProfile_type' => gettype($this->selectedProfile),
            'newPosition' => $this->newPosition,
            'newPosition_type' => gettype($this->newPosition)
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Выбор ресурса')
                    ->description('Выберите тип ресурса и город')
                    ->schema([
                        Select::make('resourceType')
                            ->label('Тип ресурса')
                            ->options([
                                'girls' => 'Индивидуалки',
                                'masseuses' => 'Массажистки',
                                'salons' => 'Интим-салоны',
                                'strip_clubs' => 'Стрип-клубы',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                Log::info('ReorderProfiles: resourceType changed', [
                                    'old' => $this->resourceType,
                                    'new' => $state,
                                    'component_id' => $this->getId()
                                ]);
                                $this->resourceType = $state;
                                // Сбрасываем выбор при изменении типа ресурса
                                $this->selectedProfile = null;
                                $this->newPosition = null;
                                
                                // Сбрасываем кэш Computed property
                                unset($this->profilesList);
                                
                                // Обновляем форму чтобы перезагрузить данные
                                $this->dispatch('$refresh');
                                
                                Log::info('ReorderProfiles: resourceType changed - cache cleared and refresh dispatched');
                            }),
                        
                        Select::make('city')
                            ->label('Город')
                            ->options([
                                'Москва' => 'Москва',
                                'Санкт-Петербург' => 'Санкт-Петербург',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                Log::info('ReorderProfiles: city changed', [
                                    'old' => $this->city,
                                    'new' => $state,
                                    'component_id' => $this->getId()
                                ]);
                                $this->city = $state;
                                // Сбрасываем выбор при изменении города
                                $this->selectedProfile = null;
                                $this->newPosition = null;
                                
                                // Сбрасываем кэш Computed property
                                unset($this->profilesList);
                                
                                // Обновляем форму чтобы перезагрузить данные
                                $this->dispatch('$refresh');
                                
                                Log::info('ReorderProfiles: city changed - cache cleared and refresh dispatched');
                            }),
                    ])
                    ->columns(2),
                
                Section::make('Выбор анкеты и позиции')
                    ->description('Выберите анкету и укажите новую позицию')
                    ->schema([
                        Select::make('selectedProfile')
                            ->label('Анкета')
                            ->options(fn () => $this->getProfileOptions())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                Log::info('ReorderProfiles: selectedProfile changed', [
                                    'old' => $this->selectedProfile,
                                    'new' => $state,
                                    'new_type' => gettype($state),
                                    'component_id' => $this->getId(),
                                    'resourceType' => $this->resourceType,
                                    'city' => $this->city
                                ]);
                                
                                // Преобразуем в int, если это строка
                                if (is_string($state) && is_numeric($state)) {
                                    $state = (int) $state;
                                }
                                
                                $this->selectedProfile = $state;
                                
                                // При изменении selectedProfile сбрасываем newPosition
                                // так как позиции зависят от выбранной анкеты
                                $this->newPosition = null;
                                
                                Log::info('ReorderProfiles: selectedProfile set', [
                                    'selectedProfile' => $this->selectedProfile,
                                    'selectedProfile_type' => gettype($this->selectedProfile),
                                    'newPosition' => $this->newPosition,
                                    'newPosition_reset' => true,
                                    'canSubmit' => !empty($this->selectedProfile) && !empty($this->newPosition)
                                ]);
                            })
                            ->disabled(fn () => empty($this->resourceType) || empty($this->city)),
                        
                        Select::make('newPosition')
                            ->label('Новая позиция')
                            ->options(function () {
                                $options = $this->getPositionOptions();
                                Log::info('ReorderProfiles: getPositionOptions called', [
                                    'options_count' => count($options),
                                    'first_few_keys' => array_slice(array_keys($options), 0, 5),
                                    'selectedProfile' => $this->selectedProfile,
                                    'profilesCount' => count($this->profilesList)
                                ]);
                                return $options;
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                Log::info('ReorderProfiles: newPosition afterStateUpdated TRIGGERED', [
                                    'old_newPosition' => $this->newPosition,
                                    'new_state' => $state,
                                    'state_type' => gettype($state),
                                    'state_is_null' => is_null($state),
                                    'state_is_empty' => empty($state),
                                    'state_is_string' => is_string($state),
                                    'state_is_numeric' => is_numeric($state),
                                    'component_id' => $this->getId(),
                                    'selectedProfile' => $this->selectedProfile,
                                    'selectedProfile_type' => gettype($this->selectedProfile),
                                    'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
                                ]);
                                
                                // Преобразуем в int, если это строка
                                $originalState = $state;
                                if (is_string($state) && is_numeric($state)) {
                                    $state = (int) $state;
                                    Log::info('ReorderProfiles: newPosition converted from string to int', [
                                        'original' => $originalState,
                                        'converted' => $state
                                    ]);
                                }
                                
                                // Устанавливаем значение напрямую
                                $this->newPosition = $state;
                                
                                // Также устанавливаем через set для формы
                                if (method_exists($set, '__invoke')) {
                                    $set('newPosition', $state);
                                }
                                
                                Log::info('ReorderProfiles: newPosition set AFTER', [
                                    'selectedProfile' => $this->selectedProfile,
                                    'selectedProfile_type' => gettype($this->selectedProfile),
                                    'newPosition' => $this->newPosition,
                                    'newPosition_type' => gettype($this->newPosition),
                                    'newPosition_is_null' => is_null($this->newPosition),
                                    'newPosition_is_empty' => empty($this->newPosition),
                                    'selectedProfile_empty' => empty($this->selectedProfile),
                                    'newPosition_empty' => empty($this->newPosition),
                                    'canSubmit' => !empty($this->selectedProfile) && !empty($this->newPosition),
                                    'all_properties' => [
                                        'resourceType' => $this->resourceType,
                                        'city' => $this->city,
                                        'selectedProfile' => $this->selectedProfile,
                                        'newPosition' => $this->newPosition
                                    ]
                                ]);
                            })
                            ->disabled(function () {
                                $disabled = empty($this->selectedProfile);
                                Log::info('ReorderProfiles: newPosition disabled check', [
                                    'disabled' => $disabled,
                                    'selectedProfile' => $this->selectedProfile,
                                    'selectedProfile_empty' => empty($this->selectedProfile)
                                ]);
                                return $disabled;
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn () => !empty($this->resourceType) && !empty($this->city)),
            ]);
    }
    
    protected function loadProfiles(): void
    {
        Log::info('ReorderProfiles: loadProfiles called', [
            'resourceType' => $this->resourceType,
            'city' => $this->city,
            'component_id' => $this->getId()
        ]);
        
        // profilesList теперь Computed property, загружается автоматически
        // Просто сбрасываем выбор если нужно
        if (empty($this->resourceType) || empty($this->city)) {
            Log::info('ReorderProfiles: loadProfiles - empty resourceType or city, clearing data');
            $this->selectedProfile = null;
            $this->newPosition = null;
            return;
        }
        
        Log::info('ReorderProfiles: loadProfiles - profiles will be loaded via Computed property', [
            'city' => $this->city,
            'resourceType' => $this->resourceType
        ]);
    }
    
    protected function getTableName(): string
    {
        $cityCode = $this->city === 'Москва' ? 'moscow' : 'spb';
        
        return match($this->resourceType) {
            'girls' => $cityCode === 'moscow' ? 'girls_moscow' : 'girls_spb',
            'masseuses' => $cityCode === 'moscow' ? 'masseuses_moscow' : 'masseuses_spb',
            'salons' => 'salons',
            'strip_clubs' => 'strip_clubs',
            default => '',
        };
    }
    
    protected function getProfileOptions(): array
    {
        $profiles = $this->profilesList;
        
        if (empty($profiles)) {
            return [];
        }
        
        $options = [];
        $idField = $this->getIdField();
        
        foreach ($profiles as $profile) {
            $position = $profile['sort_order'] ?? 999999;
            $anketaId = $profile[$idField] ?? 'N/A';
            $name = $profile['name'] ?? 'Без имени';
            
            $options[$profile['id']] = "#{$profile['id']} | {$idField}: {$anketaId} | {$name} | Позиция: {$position}";
        }
        
        return $options;
    }
    
    protected function getPositionOptions(): array
    {
        $profiles = $this->profilesList;
        
        Log::info('ReorderProfiles: getPositionOptions called', [
            'profilesList_empty' => empty($profiles),
            'profilesList_count' => count($profiles),
            'selectedProfile' => $this->selectedProfile,
            'resourceType' => $this->resourceType,
            'city' => $this->city
        ]);
        
        if (empty($profiles)) {
            Log::warning('ReorderProfiles: getPositionOptions - profilesList is empty');
            return [];
        }
        
        $options = [];
        $idField = $this->getIdField();
        
        foreach ($profiles as $index => $profile) {
            $anketaId = $profile[$idField] ?? 'N/A';
            $name = $profile['name'] ?? 'Без имени';
            $currentPosition = $index + 1;
            
            $profileId = $profile['id'] ?? null;
            if ($profileId === null) {
                Log::warning('ReorderProfiles: getPositionOptions - profile without id', [
                    'index' => $index,
                    'profile' => $profile
                ]);
                continue;
            }
            
            $options[$profileId] = "Позиция {$currentPosition} | ID: {$profileId} | {$idField}: {$anketaId} | {$name}";
        }
        
        Log::info('ReorderProfiles: getPositionOptions - options generated', [
            'options_count' => count($options),
            'first_few_keys' => array_slice(array_keys($options), 0, 5),
            'idField' => $idField
        ]);
        
        return $options;
    }
    
    protected function getModel(): ?string
    {
        return match($this->resourceType) {
            'girls' => Girl::class,
            'masseuses' => Masseuse::class,
            'salons' => Salon::class,
            'strip_clubs' => StripClub::class,
            default => null,
        };
    }
    
    public function getIdField(): string
    {
        return match($this->resourceType) {
            'girls' => 'anketa_id',
            'masseuses' => 'anketa_id',
            'salons' => 'salon_id',
            'strip_clubs' => 'club_id',
            default => 'id',
        };
    }
    
    public function logButtonState(): void
    {
        Log::info('ReorderProfiles: logButtonState() called', [
            'selectedProfile' => $this->selectedProfile,
            'newPosition' => $this->newPosition,
            'selectedProfile_type' => gettype($this->selectedProfile),
            'newPosition_type' => gettype($this->newPosition),
            'selectedProfile_empty' => empty($this->selectedProfile),
            'newPosition_empty' => empty($this->newPosition),
            'selectedProfile_is_null' => is_null($this->selectedProfile),
            'newPosition_is_null' => is_null($this->newPosition),
            'canSubmit' => !empty($this->selectedProfile) && !empty($this->newPosition),
            'resourceType' => $this->resourceType,
            'city' => $this->city,
            'profilesCount' => count($this->profilesList),
            'all_properties' => get_object_vars($this)
        ]);
    }
    
    public function updatedNewPosition($value): void
    {
        Log::info('ReorderProfiles: updatedNewPosition() called (Livewire hook)', [
            'value' => $value,
            'value_type' => gettype($value),
            'value_is_null' => is_null($value),
            'value_is_empty' => empty($value),
            'value_is_string' => is_string($value),
            'value_is_numeric' => is_numeric($value),
            'current_newPosition' => $this->newPosition,
            'selectedProfile' => $this->selectedProfile,
            'component_id' => $this->getId(),
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
        ]);
        
        // Преобразуем в int, если это строка
        $originalValue = $value;
        if (is_string($value) && is_numeric($value)) {
            $value = (int) $value;
            Log::info('ReorderProfiles: updatedNewPosition - converted to int', [
                'original' => $originalValue,
                'converted' => $value
            ]);
        }
        
        $this->newPosition = $value;
        
        Log::info('ReorderProfiles: updatedNewPosition() - value set', [
            'newPosition' => $this->newPosition,
            'newPosition_type' => gettype($this->newPosition),
            'newPosition_is_null' => is_null($this->newPosition),
            'selectedProfile' => $this->selectedProfile,
            'canSubmit' => !empty($this->selectedProfile) && !empty($this->newPosition)
        ]);
    }
    
    public function updatedSelectedProfile($value): void
    {
        Log::info('ReorderProfiles: updatedSelectedProfile() called (Livewire hook)', [
            'value' => $value,
            'value_type' => gettype($value),
            'value_is_null' => is_null($value),
            'value_is_empty' => empty($value),
            'current_selectedProfile' => $this->selectedProfile,
            'component_id' => $this->getId(),
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
        ]);
        
        // Преобразуем в int, если это строка
        $originalValue = $value;
        if (is_string($value) && is_numeric($value)) {
            $value = (int) $value;
            Log::info('ReorderProfiles: updatedSelectedProfile - converted to int', [
                'original' => $originalValue,
                'converted' => $value
            ]);
        }
        
        $this->selectedProfile = $value;
        // Сбрасываем newPosition при изменении selectedProfile
        $this->newPosition = null;
        
        Log::info('ReorderProfiles: updatedSelectedProfile() - value set', [
            'selectedProfile' => $this->selectedProfile,
            'selectedProfile_type' => gettype($this->selectedProfile),
            'newPosition_reset' => true,
            'newPosition' => $this->newPosition
        ]);
    }
    
    public function reorder(): void
    {
        Log::info('ReorderProfiles: reorder() called', [
            'selectedProfile' => $this->selectedProfile,
            'newPosition' => $this->newPosition,
            'resourceType' => $this->resourceType,
            'city' => $this->city,
            'component_id' => $this->getId()
        ]);
        
        try {
            if (empty($this->selectedProfile) || empty($this->newPosition)) {
                Log::warning('ReorderProfiles: reorder() - validation failed', [
                    'selectedProfile_empty' => empty($this->selectedProfile),
                    'newPosition_empty' => empty($this->newPosition),
                    'selectedProfile_value' => $this->selectedProfile,
                    'newPosition_value' => $this->newPosition
                ]);
                Notification::make()
                    ->title('Ошибка')
                    ->body('Выберите анкету и позицию')
                    ->danger()
                    ->send();
                return;
            }
            
            $model = $this->getModel();
            
            if (!$model) {
                throw new \Exception('Неверный тип ресурса');
            }
            
            DB::beginTransaction();
            
            // Находим профили
            $tableName = $this->getTableName();
            Log::info('ReorderProfiles: reorder() - querying database', [
                'table' => $tableName,
                'city' => $this->city,
                'resourceType' => $this->resourceType
            ]);
            
            // Используем DB::table() напрямую для надежности
            // Загружаем только необходимые поля
            $query = DB::table($tableName)
                ->select('id', 'anketa_id', 'name', 'sort_order')
                ->limit(500); // Ограничиваем количество записей для экономии памяти
            
            if (Schema::hasColumn($tableName, 'sort_order')) {
                $query->orderBy('sort_order', 'asc');
            }
            $query->orderBy('id', 'asc');
            
            $profiles = $query->get();
            
            $selectedIndex = null;
            $targetIndex = null;
            
            foreach ($profiles as $index => $profile) {
                $profileId = $profile->id;
                if ($profileId == $this->selectedProfile) {
                    $selectedIndex = $index;
                }
                if ($profileId == $this->newPosition) {
                    $targetIndex = $index;
                }
            }
            
            if ($selectedIndex === null || $targetIndex === null) {
                throw new \Exception('Не найдены выбранные профили');
            }
            
            if ($selectedIndex === $targetIndex) {
                Notification::make()
                    ->title('Без изменений')
                    ->body('Анкета уже находится на этой позиции')
                    ->warning()
                    ->send();
                DB::rollBack();
                return;
            }
            
            // Переставляем элементы в массиве
            $profilesArray = $profiles->toArray();
            $selectedProfile = $profilesArray[$selectedIndex];
            unset($profilesArray[$selectedIndex]);
            $profilesArray = array_values($profilesArray); // Перестраиваем индексы
            
            // Вставляем на новую позицию
            array_splice($profilesArray, $targetIndex, 0, [$selectedProfile]);
            
            // Обновляем sort_order для всех профилей
            $hasSortOrder = Schema::hasColumn($tableName, 'sort_order');
            
            foreach ($profilesArray as $index => $profile) {
                $profileArr = (array)$profile;
                $id = $profileArr['id'] ?? null;
                
                if (!$id) {
                    Log::warning('ReorderProfiles: profile without id', ['profile' => $profileArr]);
                    continue;
                }
                
                $updateData = [];
                
                if ($hasSortOrder) {
                    $updateData['sort_order'] = ($index + 1) * 10;
                }
                
                if (!empty($updateData)) {
                    DB::table($tableName)->where('id', $id)->update($updateData);
                }
            }
            
            DB::commit();
            
            Notification::make()
                ->title('Успешно')
                ->body('Позиция анкеты изменена')
                ->success()
                ->send();
            
            // Сбрасываем кэш Computed property и перезагружаем
            unset($this->profilesList);
            $this->selectedProfile = null;
            $this->newPosition = null;
            
            // Обновляем компонент чтобы перезагрузить данные
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Ошибка')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

