<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Инструкция</h2>
            <ul class="list-disc list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <li>Выберите тип ресурса (Индивидуалки, Массажистки, Салоны, Клубы)</li>
                <li>Выберите город (Москва или Санкт-Петербург)</li>
                <li>Выберите анкету, которую хотите переместить</li>
                <li>Выберите целевую позицию (перед каким элементом вставить)</li>
                <li>Нажмите "Применить изменения"</li>
                <li>Все анкеты между старой и новой позицией будут автоматически сдвинуты</li>
            </ul>
        </div>
        
        <form wire:submit="reorder">
            {{ $this->form }}
            
            <div class="mt-6 flex justify-end gap-3">
                <x-filament::button
                    type="submit"
                    :disabled="empty($this->selectedProfile) || empty($this->newPosition)"
                >
                    Применить изменения
                </x-filament::button>
            </div>
        </form>
        
        @if(!empty($this->profilesList))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Текущий порядок ({{ count($this->profilesList) }} анкет)</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b dark:border-gray-700">
                            <tr class="text-left">
                                <th class="pb-3 font-semibold">Позиция</th>
                                <th class="pb-3 font-semibold">ID</th>
                                <th class="pb-3 font-semibold">{{ ucfirst($this->getIdField()) }}</th>
                                <th class="pb-3 font-semibold">Имя</th>
                                <th class="pb-3 font-semibold">Sort Order</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($this->profilesList as $index => $profile)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 font-semibold">{{ $index + 1 }}</td>
                                    <td class="py-3">{{ $profile['id'] }}</td>
                                    <td class="py-3">{{ $profile[$this->getIdField()] ?? 'N/A' }}</td>
                                    <td class="py-3">{{ $profile['name'] ?? 'Без имени' }}</td>
                                    <td class="py-3">{{ $profile['sort_order'] ?? 999999 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>

