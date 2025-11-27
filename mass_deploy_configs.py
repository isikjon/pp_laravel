#!/usr/bin/env python3
import sqlite3
import os
import subprocess
from pathlib import Path

def generate_nginx_config(subdomain, project_path, server_ip, php_fpm_sock, ssl_cert, ssl_key):
    return f"""server {{
	server_name {subdomain} www.{subdomain};
	charset off;
	index index.php index.html;
	disable_symlinks if_not_owner from=$root_path;
	include /etc/nginx/vhosts-includes/*.conf;
	include /etc/nginx/vhosts-resources/{subdomain}/*.conf;
	include /etc/nginx/users-resources/noviysayt/*.conf;
	access_log /var/www/httpd-logs/{subdomain}.access.log;
	error_log /var/www/httpd-logs/{subdomain}.error.log notice;
	ssi on;
	set $root_path {project_path}/public;
	root $root_path;
	listen {server_ip}:80;
	gzip on;
	gzip_comp_level 5;
	gzip_disable "msie6";
	gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript application/javascript image/svg+xml;
	
	location / {{
		try_files $uri $uri/ /index.php?$query_string;
		
		location ~ [^/]\\.ph(p\\d*|tml)$ {{
			try_files /does_not_exists @php;
		}}
		
		location ~* ^.+\\.(jpg|jpeg|gif|png|svg|js|css|mp3|ogg|mpe?g|avi|zip|gz|bz2?|rar|swf|webp|woff|woff2)$ {{
			expires 24h;
		}}
	}}
	
	location @php {{
		include /etc/nginx/vhosts-resources/{subdomain}/dynamic/*.conf;
		fastcgi_index index.php;
		fastcgi_param PHP_ADMIN_VALUE "sendmail_path = /usr/sbin/sendmail -t -i -f webmaster@{subdomain}";
		fastcgi_pass unix:{php_fpm_sock};
		fastcgi_split_path_info ^((?U).+\\.ph(?:p\\d*|tml))(/?.+)$;
		try_files $uri =404;
		include fastcgi_params;
	}}
	
	return 301 https://$host$request_uri;
}}

server {{
	server_name {subdomain} www.{subdomain};
	ssl_certificate "{ssl_cert}";
	ssl_certificate_key "{ssl_key}";
	ssl_ciphers EECDH:+AES256:-3DES:RSA+AES:!NULL:!RC4;
	ssl_prefer_server_ciphers on;
	ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
	ssl_dhparam /etc/ssl/certs/dhparam4096.pem;
	charset off;
	index index.php index.html;
	disable_symlinks if_not_owner from=$root_path;
	include /etc/nginx/vhosts-includes/*.conf;
	include /etc/nginx/vhosts-resources/{subdomain}/*.conf;
	include /etc/nginx/users-resources/noviysayt/*.conf;
	access_log /var/www/httpd-logs/{subdomain}.access.log;
	error_log /var/www/httpd-logs/{subdomain}.error.log notice;
	ssi on;
	set $root_path {project_path}/public;
	root $root_path;
	listen {server_ip}:443 ssl;
	gzip on;
	gzip_comp_level 5;
	gzip_disable "msie6";
	gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript application/javascript image/svg+xml;
	
	add_header X-Frame-Options "SAMEORIGIN";
	add_header X-Content-Type-Options "nosniff";
	add_header Strict-Transport-Security "max-age=31536000;";
	
	location / {{
		try_files $uri $uri/ /index.php?$query_string;
		
		location ~ [^/]\\.ph(p\\d*|tml)$ {{
			try_files /does_not_exists @php;
		}}
		
		location ~* ^.+\\.(jpg|jpeg|gif|png|svg|js|css|mp3|ogg|mpe?g|avi|zip|gz|bz2?|rar|swf|webp|woff|woff2)$ {{
			expires 24h;
		}}
	}}
	
	location ~ /\\.(?!well-known).* {{
		deny all;
	}}
	
	location @php {{
		include /etc/nginx/vhosts-resources/{subdomain}/dynamic/*.conf;
		fastcgi_index index.php;
		fastcgi_param PHP_ADMIN_VALUE "sendmail_path = /usr/sbin/sendmail -t -i -f webmaster@{subdomain}";
		fastcgi_pass unix:{php_fpm_sock};
		fastcgi_split_path_info ^((?U).+\\.ph(?:p\\d*|tml))(/?.+)$;
		try_files $uri =404;
		include fastcgi_params;
	}}
}}
"""

def main():
    db_path = Path(__file__).parent / 'database' / 'database.sqlite'
    
    if not db_path.exists():
        print(f"Ошибка: База данных не найдена по пути {db_path}")
        return
    
    conn = sqlite3.connect(str(db_path))
    cursor = conn.cursor()
    
    cursor.execute("SELECT name, subdomain FROM cities WHERE is_active = 1 ORDER BY name")
    cities = cursor.fetchall()
    conn.close()
    
    domain = "prostitutkimoskvytake.org"
    nginx_dir = "/etc/nginx/vhosts/noviysayt"
    
    project_path = "/var/www/noviysayt/data/www/prostitutkimoskvytake.org"
    server_ip = "45.82.66.116"
    php_fpm_sock = "/var/www/php-fpm/3584.sock"
    ssl_cert = "/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.crtca"
    ssl_key = "/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.key"
    
    print("Массовое создание конфигов Nginx...\n")
    
    created = []
    skipped = []
    
    for city_name, subdomain in cities:
        full_domain = f"{subdomain}.{domain}" if subdomain else domain
        config_path = f"{nginx_dir}/{full_domain}.conf"
        
        if os.path.exists(config_path):
            print(f"⊘ {full_domain} - конфиг уже существует")
            skipped.append((city_name, full_domain))
            continue
        
        print(f"Создание конфига: {full_domain}")
        
        config_content = generate_nginx_config(
            full_domain, project_path, server_ip, php_fpm_sock, ssl_cert, ssl_key
        )
        
        try:
            with open(config_path, 'w') as f:
                f.write(config_content)
            
            resources_dir = f"/etc/nginx/vhosts-resources/{full_domain}/dynamic"
            os.makedirs(resources_dir, exist_ok=True)
            
            print(f"  ✓ Конфиг создан: {config_path}")
            print(f"  ✓ Директория создана: {resources_dir}")
            
            created.append((city_name, full_domain))
            
        except PermissionError:
            print(f"  ✗ Нет прав для создания конфига (требуется sudo)")
            print(f"\nЗапустите скрипт с sudo:")
            print(f"sudo python3 {__file__}")
            return
        except Exception as e:
            print(f"  ✗ Ошибка: {e}")
    
    print("\n=== ИТОГИ ===\n")
    print(f"Создано конфигов: {len(created)}")
    for city, subdomain in created:
        print(f"  ✓ {subdomain} ({city})")
    
    print(f"\nПропущено (уже существуют): {len(skipped)}")
    for city, subdomain in skipped:
        print(f"  ⊘ {subdomain} ({city})")
    
    if created:
        print("\n=== СЛЕДУЮЩИЕ ШАГИ ===\n")
        print("1. Проверьте конфигурацию Nginx:")
        print("   sudo nginx -t")
        print("\n2. Если всё ОК, перезагрузите Nginx:")
        print("   sudo systemctl reload nginx")
        print("\n3. Все поддомены уже работают с SSL (wildcard сертификат)")

if __name__ == '__main__':
    main()

