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
    public array $profilesList = [];
    
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
                                $this->loadProfiles();
                                Log::info('ReorderProfiles: after loadProfiles', [
                                    'resourceType' => $this->resourceType,
                                    'city' => $this->city,
                                    'profilesCount' => count($this->profilesList)
                                ]);
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
                                $this->loadProfiles();
                                Log::info('ReorderProfiles: after loadProfiles', [
                                    'resourceType' => $this->resourceType,
                                    'city' => $this->city,
                                    'profilesCount' => count($this->profilesList)
                                ]);
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
                            ->options(fn () => $this->getPositionOptions())
                            ->searchable()
                            ->required()
                            ->afterStateUpdated(function ($state) {
                                Log::info('ReorderProfiles: newPosition changed', [
                                    'old' => $this->newPosition,
                                    'new' => $state,
                                    'new_type' => gettype($state),
                                    'component_id' => $this->getId(),
                                    'selectedProfile' => $this->selectedProfile
                                ]);
                                
                                // Преобразуем в int, если это строка
                                if (is_string($state) && is_numeric($state)) {
                                    $state = (int) $state;
                                }
                                
                                $this->newPosition = $state;
                                
                                Log::info('ReorderProfiles: newPosition set', [
                                    'selectedProfile' => $this->selectedProfile,
                                    'selectedProfile_type' => gettype($this->selectedProfile),
                                    'newPosition' => $this->newPosition,
                                    'newPosition_type' => gettype($this->newPosition),
                                    'selectedProfile_empty' => empty($this->selectedProfile),
                                    'newPosition_empty' => empty($this->newPosition),
                                    'canSubmit' => !empty($this->selectedProfile) && !empty($this->newPosition)
                                ]);
                            })
                            ->disabled(fn () => empty($this->selectedProfile)),
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
        
        if (empty($this->resourceType) || empty($this->city)) {
            Log::info('ReorderProfiles: loadProfiles - empty resourceType or city, clearing data');
            $this->profilesList = [];
            $this->selectedProfile = null;
            $this->newPosition = null;
            return;
        }
        
        $model = $this->getModel();
        
        if (!$model) {
            Log::warning('ReorderProfiles: loadProfiles - model not found', [
                'resourceType' => $this->resourceType
            ]);
            $this->profilesList = [];
            $this->selectedProfile = null;
            $this->newPosition = null;
            return;
        }
        
        try {
            $tableName = $this->getTableName();
            Log::info('ReorderProfiles: loadProfiles - querying database', [
                'table' => $tableName,
                'city' => $this->city,
                'model' => $model
            ]);
            
            $query = $model::where('city', $this->city);
            
            // Проверяем наличие колонки sort_order
            if (Schema::hasColumn($tableName, 'sort_order')) {
                $query->orderBy('sort_order', 'asc');
            }
            $query->orderBy('id', 'asc');
            
            $profiles = $query->get();
            $this->profilesList = $profiles->toArray();
            
            Log::info('ReorderProfiles: loadProfiles - profiles loaded', [
                'count' => count($this->profilesList),
                'first_few_ids' => array_slice(array_column($this->profilesList, 'id'), 0, 5),
                'before_clear_selectedProfile' => $this->selectedProfile,
                'before_clear_newPosition' => $this->newPosition
            ]);
            
            // НЕ сбрасываем selectedProfile и newPosition, если они уже установлены
            // Сбрасываем только если список профилей изменился (новый resourceType или city)
            // Это позволит сохранить выбор пользователя
            // $this->selectedProfile = null;
            // $this->newPosition = null;
            
            Log::info('ReorderProfiles: loadProfiles - profiles loaded, keeping user selections', [
                'selectedProfile' => $this->selectedProfile,
                'newPosition' => $this->newPosition
            ]);
        } catch (\Exception $e) {
            Log::error('ReorderProfiles::loadProfiles error: ' . $e->getMessage(), [
                'resourceType' => $this->resourceType,
                'city' => $this->city,
                'trace' => $e->getTraceAsString()
            ]);
            $this->profilesList = [];
            $this->selectedProfile = null;
            $this->newPosition = null;
        }
    }
    
    protected function getTableName(): string
    {
        return match($this->resourceType) {
            'girls' => 'girls',
            'masseuses' => 'masseuses',
            'salons' => 'salons',
            'strip_clubs' => 'strip_clubs',
            default => '',
        };
    }
    
    protected function getProfileOptions(): array
    {
        if (empty($this->profilesList)) {
            return [];
        }
        
        $options = [];
        $idField = $this->getIdField();
        
        foreach ($this->profilesList as $profile) {
            $position = $profile['sort_order'] ?? 999999;
            $anketaId = $profile[$idField] ?? 'N/A';
            $name = $profile['name'] ?? 'Без имени';
            
            $options[$profile['id']] = "#{$profile['id']} | {$idField}: {$anketaId} | {$name} | Позиция: {$position}";
        }
        
        return $options;
    }
    
    protected function getPositionOptions(): array
    {
        if (empty($this->profilesList)) {
            return [];
        }
        
        $options = [];
        $idField = $this->getIdField();
        
        foreach ($this->profilesList as $index => $profile) {
            $anketaId = $profile[$idField] ?? 'N/A';
            $name = $profile['name'] ?? 'Без имени';
            $currentPosition = $index + 1;
            
            $options[$profile['id']] = "Позиция {$currentPosition} | ID: {$profile['id']} | {$idField}: {$anketaId} | {$name}";
        }
        
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
            'canSubmit' => !empty($this->selectedProfile) && !empty($this->newPosition),
            'resourceType' => $this->resourceType,
            'city' => $this->city,
            'profilesCount' => count($this->profilesList)
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
            $query = $model::where('city', $this->city);
            
            if (Schema::hasColumn($tableName, 'sort_order')) {
                $query->orderBy('sort_order', 'asc');
            }
            $query->orderBy('id', 'asc');
            
            $profiles = $query->get();
            
            $selectedIndex = null;
            $targetIndex = null;
            
            foreach ($profiles as $index => $profile) {
                if ($profile->id == $this->selectedProfile) {
                    $selectedIndex = $index;
                }
                if ($profile->id == $this->newPosition) {
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
            $selectedProfile = $profiles[$selectedIndex];
            $profiles->forget($selectedIndex);
            $profiles = $profiles->values(); // Перестраиваем индексы
            
            // Вставляем на новую позицию
            $profiles->splice($targetIndex, 0, [$selectedProfile]);
            
            // Обновляем sort_order для всех профилей
            $tableName = $this->getTableName();
            $hasSortOrder = Schema::hasColumn($tableName, 'sort_order');
            
            foreach ($profiles as $index => $profile) {
                if ($hasSortOrder) {
                    $profile->sort_order = ($index + 1) * 10;
                }
                $profile->save();
            }
            
            DB::commit();
            
            Notification::make()
                ->title('Успешно')
                ->body('Позиция анкеты изменена')
                ->success()
                ->send();
            
            // Перезагружаем список
            $this->loadProfiles();
            $this->selectedProfile = null;
            $this->newPosition = null;
            
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

