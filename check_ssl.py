#!/usr/bin/env python3
import sqlite3
import ssl
import socket
import sys
from pathlib import Path

def check_ssl(domain):
    context = ssl.create_default_context()
    context.check_hostname = False
    context.verify_mode = ssl.CERT_NONE
    
    try:
        with socket.create_connection((domain, 443), timeout=10) as sock:
            with context.wrap_socket(sock, server_hostname=domain) as ssock:
                return True
    except Exception:
        return False

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
    
    print("Проверка SSL для всех поддоменов...\n")
    
    results = []
    
    for city_name, subdomain in cities:
        full_domain = f"{subdomain}.{domain}" if subdomain else domain
        
        print(f"Проверка: {full_domain}")
        
        has_ssl = check_ssl(full_domain)
        
        results.append({
            'city': city_name,
            'subdomain': full_domain,
            'has_ssl': has_ssl
        })
        
        if has_ssl:
            print(f"  ✓ SSL активен")
        else:
            print(f"  ✗ SSL отсутствует")
    
    print("\n=== ИТОГИ ===\n")
    
    with_ssl = [r for r in results if r['has_ssl']]
    without_ssl = [r for r in results if not r['has_ssl']]
    
    print(f"С SSL: {len(with_ssl)}")
    for item in with_ssl:
        print(f"  ✓ {item['subdomain']} ({item['city']})")
    
    print(f"\nБез SSL: {len(without_ssl)}")
    for item in without_ssl:
        print(f"  ✗ {item['subdomain']} ({item['city']})")

if __name__ == '__main__':
    main()

