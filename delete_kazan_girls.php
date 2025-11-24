<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tableName = 'girls_kazan';

if (!Schema::hasTable($tableName)) {
    echo "❌ Таблица {$tableName} не существует\n";
    exit(1);
}

$countBefore = DB::table($tableName)->count();
echo "📊 Количество записей до удаления: {$countBefore}\n";

if ($countBefore === 0) {
    echo "✅ Таблица уже пустая\n";
    exit(0);
}

echo "🗑️  Удаление всех записей из {$tableName}...\n";

DB::table($tableName)->delete();

$countAfter = DB::table($tableName)->count();
echo "✅ Удалено: {$countBefore} записей\n";
echo "📊 Количество записей после удаления: {$countAfter}\n";

$city = \App\Models\City::where('code', 'kazan')->first();
if ($city) {
    $city->update(['girls_count' => 0]);
    echo "✅ Обновлен счетчик девушек для города Казань\n";
}

echo "\n✅ Готово!\n";

