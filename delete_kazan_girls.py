#!/usr/bin/env python3

import sqlite3
import os
import sys

db_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'database', 'database.sqlite')

if not os.path.exists(db_path):
    print(f"❌ База данных не найдена: {db_path}")
    sys.exit(1)

try:
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    
    table_name = 'girls_kazan'
    
    cursor.execute(f"SELECT name FROM sqlite_master WHERE type='table' AND name='{table_name}'")
    if not cursor.fetchone():
        print(f"❌ Таблица {table_name} не существует")
        conn.close()
        sys.exit(1)
    
    cursor.execute(f"SELECT COUNT(*) FROM {table_name}")
    count_before = cursor.fetchone()[0]
    
    print(f"📊 Количество записей до удаления: {count_before}")
    
    if count_before == 0:
        print("✅ Таблица уже пустая")
        conn.close()
        sys.exit(0)
    
    print(f"🗑️  Удаление всех записей из {table_name}...")
    
    cursor.execute(f"DELETE FROM {table_name}")
    
    cursor.execute(f"SELECT COUNT(*) FROM {table_name}")
    count_after = cursor.fetchone()[0]
    
    cursor.execute("SELECT id FROM cities WHERE code = 'kazan'")
    city_row = cursor.fetchone()
    
    if city_row:
        city_id = city_row[0]
        cursor.execute("UPDATE cities SET girls_count = 0 WHERE id = ?", (city_id,))
        print("✅ Обновлен счетчик девушек для города Казань")
    
    conn.commit()
    
    print(f"✅ Удалено: {count_before} записей")
    print(f"📊 Количество записей после удаления: {count_after}")
    print("\n✅ Готово!")
    
    conn.close()
    
except sqlite3.Error as e:
    print(f"❌ Ошибка SQLite: {e}")
    sys.exit(1)
except Exception as e:
    print(f"❌ Ошибка: {e}")
    sys.exit(1)

