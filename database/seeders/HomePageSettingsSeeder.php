<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomePageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\HomePageSettings::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'ProstitutkiMoscow',
                'description' => 'Каталог анкет с подробными фильтрами и проверенными предложениями в Москве и Санкт-Петербурге.',
            ]
        );
    }
}
