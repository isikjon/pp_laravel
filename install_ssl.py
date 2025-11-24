#!/usr/bin/env python3
import sqlite3
import subprocess
import os
import sys

DB_PATH = '/var/www/noviysayt/data/www/prostitutkimoskvytake.org/database/database.sqlite'
NGINX_VHOSTS = '/etc/nginx/vhosts/noviysayt'
BASE_DOMAIN = 'prostitutkimoskvytake.org'
EMAIL = 'admin@prostitutkimoskvytake.org'

def get_active_cities():
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("SELECT code, name, subdomain FROM cities WHERE is_active = 1")
    cities = cursor.fetchall()
    conn.close()
    return cities

def check_ssl_exists(domain):
    cert_path = f'/etc/letsencrypt/live/{domain}/fullchain.pem'
    try:
        return os.path.exists(cert_path)
    except:
        return False

def check_nginx_config_exists(domain):
    config_path = f'{NGINX_VHOSTS}/{domain}.conf'
    try:
        return os.path.exists(config_path)
    except:
        return False

def install_ssl(domain):
    webroot_path = '/var/www/noviysayt/data/www/prostitutkimoskvytake.org/public'
    
    try:
        cmd = [
            'certbot', 'certonly',
            '--webroot',
            '-w', webroot_path,
            '-d', domain,
            '--email', EMAIL,
            '--agree-tos',
            '--non-interactive'
        ]
        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode == 0:
            print(f'✅ SSL установлен для {domain}')
            return True
        else:
            print(f'❌ Ошибка установки SSL для {domain}')
            if result.stderr:
                lines = result.stderr.strip().split('\n')
                for line in lines[-5:]:
                    if line.strip():
                        print(f'   {line}')
            return False
    except Exception as e:
        print(f'❌ Исключение при установке SSL для {domain}: {str(e)}')
        return False

def reload_nginx():
    try:
        result = subprocess.run(['nginx', '-t'], capture_output=True, text=True)
        if result.returncode != 0:
            print('❌ Ошибка конфигурации Nginx')
            return False
        subprocess.run(['systemctl', 'reload', 'nginx'], check=True, capture_output=True)
        print('✅ Nginx перезагружен')
        return True
    except:
        print('❌ Ошибка перезагрузки Nginx')
        return False

def main():
    print('🔍 Получение списка активных городов...')
    cities = get_active_cities()
    
    if not cities:
        print('❌ Нет активных городов в базе данных')
        sys.exit(1)
    
    domains = []
    for code, name, subdomain in cities:
        domain = f'{subdomain}.{BASE_DOMAIN}' if subdomain else BASE_DOMAIN
        domains.append((domain, name))
    
    print(f'\n📋 Найдено доменов: {len(domains)}')
    for domain, name in domains:
        print(f'  - {domain} ({name})')
    
    print('\n🔐 Проверка SSL сертификатов...\n')
    
    domains_without_ssl = []
    for domain, name in domains:
        if check_ssl_exists(domain):
            print(f'✅ {domain} - SSL уже установлен')
        else:
            print(f'❌ {domain} - SSL отсутствует')
            if check_nginx_config_exists(domain):
                domains_without_ssl.append((domain, name))
            else:
                print(f'   ⚠️  Nginx конфиг отсутствует, пропускаем')
    
    if not domains_without_ssl:
        print('\n✅ Все домены уже имеют SSL сертификаты')
        sys.exit(0)
    
    print(f'\n🚀 Установка SSL для {len(domains_without_ssl)} доменов...\n')
    
    success_count = 0
    for domain, name in domains_without_ssl:
        print(f'📦 Устанавливаю SSL для {domain} ({name})...')
        if install_ssl(domain):
            success_count += 1
    
    print(f'\n📊 Результат: {success_count}/{len(domains_without_ssl)} успешно')
    
    if success_count > 0:
        print('\n🔄 Перезагрузка Nginx...')
        reload_nginx()
    
    print('\n✅ Готово!')

if __name__ == '__main__':
    main()

