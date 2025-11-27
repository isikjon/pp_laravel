#!/usr/bin/env python3
import sqlite3
import os
import sys
from pathlib import Path

def main():
    db_path = Path(__file__).parent / 'database' / 'database.sqlite'
    
    if not db_path.exists():
        print(f"Ошибка: База данных не найдена по пути {db_path}")
        sys.exit(1)
    
    conn = sqlite3.connect(str(db_path))
    cursor = conn.cursor()
    
    cursor.execute("SELECT name, subdomain FROM cities WHERE is_active = 1 ORDER BY name")
    cities = cursor.fetchall()
    conn.close()
    
    domain = "prostitutkimoskvytake.org"
    nginx_dirs = [
        '/etc/nginx/conf.d',
        '/etc/nginx/sites-enabled',
        '/etc/nginx/sites-available',
        Path.home() / 'conf' / 'web',
    ]
    
    print("Проверка конфигов Nginx для всех поддоменов...\n")
    
    results = []
    
    for city_name, subdomain in cities:
        full_domain = f"{subdomain}.{domain}" if subdomain else domain
        
        print(f"Проверка: {full_domain}")
        
        found_configs = []
        
        for nginx_dir in nginx_dirs:
            if not os.path.exists(nginx_dir):
                continue
            
            config_variants = [
                os.path.join(nginx_dir, f"{full_domain}.conf"),
                os.path.join(nginx_dir, f"{full_domain}.ssl.conf"),
                os.path.join(nginx_dir, full_domain),
            ]
            
            for config_path in config_variants:
                if os.path.exists(config_path):
                    found_configs.append(config_path)
        
        results.append({
            'city': city_name,
            'subdomain': full_domain,
            'configs': found_configs
        })
        
        if found_configs:
            print(f"  ✓ Найдено конфигов: {len(found_configs)}")
            for config in found_configs:
                print(f"    - {config}")
        else:
            print(f"  ✗ Конфиги не найдены")
    
    print("\n=== ИТОГИ ===\n")
    
    with_configs = [r for r in results if r['configs']]
    without_configs = [r for r in results if not r['configs']]
    
    print(f"С конфигами: {len(with_configs)}")
    for item in with_configs:
        print(f"  ✓ {item['subdomain']} ({item['city']}) - {len(item['configs'])} файл(ов)")
    
    print(f"\nБез конфигов: {len(without_configs)}")
    for item in without_configs:
        print(f"  ✗ {item['subdomain']} ({item['city']})")
    
    print("\n=== ДИРЕКТОРИИ NGINX ===\n")
    for nginx_dir in nginx_dirs:
        if os.path.exists(nginx_dir):
            print(f"✓ {nginx_dir} (существует)")
            try:
                files = os.listdir(nginx_dir)
                print(f"  Файлов: {len(files)}")
            except PermissionError:
                print(f"  Нет доступа для чтения")
        else:
            print(f"✗ {nginx_dir} (не существует)")

if __name__ == '__main__':
    main()

