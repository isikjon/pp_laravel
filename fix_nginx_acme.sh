#!/bin/bash

WEBROOT="/var/www/noviysayt/data/www/prostitutkimoskvytake.org/public"
NGINX_VHOSTS="/etc/nginx/vhosts/noviysayt"

echo "🔧 Исправление nginx конфигов для SSL"
echo "======================================"

DOMAINS=$(python3 << 'EOF'
import sqlite3
db_path = "/var/www/noviysayt/data/www/prostitutkimoskvytake.org/database/database.sqlite"
conn = sqlite3.connect(db_path)
cursor = conn.cursor()
cursor.execute("SELECT subdomain, name FROM cities WHERE is_active = 1")
for row in cursor.fetchall():
    subdomain = row[0] if row[0] else ""
    name = row[1]
    base_domain = "prostitutkimoskvytake.org"
    domain = f"{subdomain}.{base_domain}" if subdomain else base_domain
    print(f"{domain}|{name}")
conn.close()
EOF
)

FIXED=0
SKIPPED=0

while IFS='|' read -r domain name; do
    CONFIG_FILE="$NGINX_VHOSTS/$domain.conf"
    
    if [ ! -f "$CONFIG_FILE" ]; then
        echo "⚠️  $domain - конфиг не найден"
        ((SKIPPED++))
        continue
    fi
    
    if grep -q "location.*acme-challenge" "$CONFIG_FILE"; then
        echo "✅ $domain - уже исправлен"
        ((SKIPPED++))
        continue
    fi
    
    echo "🔧 Исправляю $domain..."
    
    cp "$CONFIG_FILE" "$CONFIG_FILE.backup.$(date +%s)"
    
    sed -i "/location ~ \/\\\.(?!well-known)/i\\    location ^~ /.well-known/acme-challenge/ {\n        root $WEBROOT;\n        allow all;\n    }\n" "$CONFIG_FILE"
    
    if grep -q "acme-challenge" "$CONFIG_FILE"; then
        echo "   ✅ Исправлен"
        ((FIXED++))
    else
        echo "   ❌ Не удалось исправить"
        ((SKIPPED++))
    fi
    
done <<< "$DOMAINS"

echo ""
echo "======================================"
echo "📊 Результат: исправлено=$FIXED, пропущено=$SKIPPED"
echo ""
echo "🧪 Проверка конфигурации nginx..."
nginx -t

if [ $? -eq 0 ]; then
    echo ""
    echo "🔄 Перезагрузка nginx..."
    systemctl reload nginx
    echo "✅ Nginx перезагружен"
    echo ""
    echo "🎉 Готово! Теперь можно устанавливать SSL:"
    echo "   ./install_ssl.sh"
else
    echo ""
    echo "❌ Ошибка в конфигурации nginx!"
    echo "   Восстанови backup файлы вручную"
fi

