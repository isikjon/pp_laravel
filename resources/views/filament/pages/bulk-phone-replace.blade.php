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
        
        @php
            $city = $this->data['city'] ?? null;
            $resourceType = $this->data['resource_type'] ?? null;
            $profiles = $this->profilesList;
        @endphp
        
        @if(!empty($city) && !empty($resourceType) && !empty($profiles))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Текущий порядок ({{ count($profiles) }} анкет)</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b dark:border-gray-700">
                            <tr class="text-left">
                                <th class="pb-3 font-semibold">Позиция</th>
                                <th class="pb-3 font-semibold">ID</th>
                                <th class="pb-3 font-semibold">{{ ucfirst($this->getIdField($resourceType)) }}</th>
                                <th class="pb-3 font-semibold">Имя</th>
                                <th class="pb-3 font-semibold">Sort Order</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($profiles as $index => $profile)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 font-semibold">{{ $index + 1 }}</td>
                                    <td class="py-3">{{ $profile['id'] }}</td>
                                    <td class="py-3">{{ $profile['anketa_id'] ?? 'N/A' }}</td>
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

