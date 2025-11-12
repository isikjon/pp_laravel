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
    
    public array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
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
                                $this->resourceType = $state;
                                $this->loadProfiles();
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
                                $this->city = $state;
                                $this->loadProfiles();
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
                                $this->selectedProfile = $state;
                            })
                            ->disabled(fn () => empty($this->resourceType) || empty($this->city)),
                        
                        Select::make('newPosition')
                            ->label('Новая позиция')
                            ->options(fn () => $this->getPositionOptions())
                            ->searchable()
                            ->required()
                            ->afterStateUpdated(function ($state) {
                                $this->newPosition = $state;
                            })
                            ->disabled(fn () => empty($this->selectedProfile)),
                    ])
                    ->columns(2)
                    ->visible(fn () => !empty($this->resourceType) && !empty($this->city)),
            ])
            ->statePath('data');
    }
    
    protected function loadProfiles(): void
    {
        if (empty($this->resourceType) || empty($this->city)) {
            $this->profilesList = [];
            $this->selectedProfile = null;
            $this->newPosition = null;
            return;
        }
        
        $model = $this->getModel();
        
        if (!$model) {
            $this->profilesList = [];
            $this->selectedProfile = null;
            $this->newPosition = null;
            return;
        }
        
        try {
            $tableName = $this->getTableName();
            $query = $model::where('city', $this->city);
            
            // Проверяем наличие колонки sort_order
            if (Schema::hasColumn($tableName, 'sort_order')) {
                $query->orderBy('sort_order', 'asc');
            }
            $query->orderBy('id', 'asc');
            
            $this->profilesList = $query->get()->toArray();
            $this->selectedProfile = null;
            $this->newPosition = null;
        } catch (\Exception $e) {
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
    
    protected function getIdField(): string
    {
        return match($this->resourceType) {
            'girls' => 'anketa_id',
            'masseuses' => 'anketa_id',
            'salons' => 'salon_id',
            'strip_clubs' => 'club_id',
            default => 'id',
        };
    }
    
    public function reorder(): void
    {
        try {
            if (empty($this->selectedProfile) || empty($this->newPosition)) {
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

