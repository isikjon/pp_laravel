<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Инструкция по использованию
                </h3>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <p><strong>1.</strong> Выберите тип ресурса (Girls, Masseuses, Salons, Strip Clubs)</p>
                    <p><strong>2.</strong> Выберите диапазон записей для замены:</p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li><strong>Все записи</strong> - заменит номер для всех записей в базе</li>
                        <li><strong>Первые 500</strong> - заменит номер для первых 500 записей (по ID)</li>
                        <li><strong>Первые 1000</strong> - заменит номер для первых 1000 записей (по ID)</li>
                        <li><strong>Произвольный диапазон</strong> - выберите начальную и конечную запись вручную</li>
                    </ul>
                    <p><strong>3.</strong> Введите новый номер телефона в формате +7(999)999-99-99</p>
                    <p><strong>4.</strong> Нажмите "Заменить номера" и подтвердите действие</p>
                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-yellow-800 dark:text-yellow-200">
                            <strong>⚠️ Внимание:</strong> Массовая замена номеров - это необратимое действие! Убедитесь, что выбрали правильный диапазон перед подтверждением.
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

