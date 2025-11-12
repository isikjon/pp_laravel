<?php

namespace App\Filament\Pages;

use App\Models\Girl;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Выбор диапазона')
                    ->schema([
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
                            ->afterStateUpdated(fn ($state, callable $set) => [
                                $set('range_type', null),
                                $set('from_id', null),
                                $set('to_id', null),
                            ]),
                        
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
        $model = $this->getModelClass($resourceType);
        
        $query = $model::query()
            ->select(['id', 'name'])
            ->limit(50);
        
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        return $query->get()
            ->mapWithKeys(fn ($record) => [
                $record->id => "ID: {$record->id} - {$record->name}"
            ])
            ->toArray();
    }

    protected function getRecordLabel(string $resourceType, $id): string
    {
        $model = $this->getModelClass($resourceType);
        $record = $model::find($id);
        
        if (!$record) {
            return "ID: {$id}";
        }
        
        return "ID: {$record->id} - {$record->name}";
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

    public function replacePhones(): void
    {
        $data = $this->form->getState();
        
        $resourceType = $data['resource_type'];
        $rangeType = $data['range_type'];
        $newPhone = $data['new_phone'];
        
        $model = $this->getModelClass($resourceType);
        
        try {
            DB::beginTransaction();
            
            $query = $model::query();
            
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
                
                return;
            }
            
            // Выполняем массовое обновление
            $model::whereIn('id', $recordIds)->update(['phone' => $newPhone]);
            
            DB::commit();
            
            Notification::make()
                ->success()
                ->title('Успешно!')
                ->body("Номер телефона обновлен для {$count} записей.")
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
                    $rangeType = $data['range_type'] ?? 'all';
                    
                    $message = match ($rangeType) {
                        'all' => 'Вы собираетесь заменить номер для ВСЕХ записей.',
                        'first_500' => 'Вы собираетесь заменить номер для первых 500 записей.',
                        'first_1000' => 'Вы собираетесь заменить номер для первых 1000 записей.',
                        'custom' => 'Вы собираетесь заменить номер для записей в выбранном диапазоне.',
                        default => 'Вы собираетесь выполнить массовую замену номеров.',
                    };
                    
                    return $message . ' Это действие нельзя отменить!';
                })
                ->modalSubmitActionLabel('Да, заменить')
                ->modalCancelActionLabel('Отмена')
                ->action('replacePhones')
                ->submit('replace'),
        ];
    }
}

