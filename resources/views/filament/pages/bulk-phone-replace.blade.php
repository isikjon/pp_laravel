<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Инструкция по использованию
                </h3>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <p><strong>1.</strong> Выберите город</p>
                    <p><strong>2.</strong> Выберите тип ресурса</p>
                    <p><strong>3.</strong> Выберите диапазон</p>
                    <p><strong>4.</strong> Введите новый номер</p>
                    <p><strong>5.</strong> Нажмите "Заменить номера"</p>
                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-yellow-800 dark:text-yellow-200">
                            <strong>Внимание:</strong> Необратимое действие!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <x-filament-panels::form wire:submit="replacePhones">
            {{ $this->form }}
            
            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
                :full-width="true"
            />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>

