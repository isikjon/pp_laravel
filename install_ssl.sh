#!/bin/bash

DB_PATH="/var/www/noviysayt/data/www/prostitutkimoskvytake.org/database/database.sqlite"
WEBROOT="/var/www/noviysayt/data/www/prostitutkimoskvytake.org/public"
BASE_DOMAIN="prostitutkimoskvytake.org"
EMAIL="admin@prostitutkimoskvytake.org"

echo "🔍 Получение списка активных городов..."

DOMAINS=$(python3 << 'EOF'
import sqlite3
db_path = "/var/www/noviysayt/data/www/prostitutkimoskvytake.org/database/database.sqlite"
conn = sqlite3.connect(db_path)
cursor = conn.cursor()
cursor.execute("SELECT subdomain, name FROM cities WHERE is_active = 1")
for row in cursor.fetchall():
    subdomain = row[0] if row[0] else ""
    name = row[1]
    print(f"{subdomain}|{name}")
conn.close()
EOF
)

if [ -z "$DOMAINS" ]; then
    echo "❌ Нет активных городов в базе данных"
    exit 1
fi

SUCCESS=0
FAILED=0

while IFS='|' read -r subdomain name; do
    if [ -z "$subdomain" ]; then
        DOMAIN="$BASE_DOMAIN"
    else
        DOMAIN="${subdomain}.${BASE_DOMAIN}"
    fi
    
    if [ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
        echo "✅ $DOMAIN ($name) - SSL уже установлен"
        continue
    fi
    
    echo "📦 Устанавливаю SSL для $DOMAIN ($name)..."
    
    certbot certonly \
        --webroot \
        -w "$WEBROOT" \
        -d "$DOMAIN" \
        --email "$EMAIL" \
        --agree-tos \
        --non-interactive \
        --force-renewal 2>&1 | grep -E "(Successfully|error|failed)" || true
    
    if [ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
        echo "✅ SSL успешно установлен для $DOMAIN"
        ((SUCCESS++))
    else
        echo "❌ Ошибка установки SSL для $DOMAIN"
        ((FAILED++))
    fi
    
done <<< "$DOMAINS"

echo ""
echo "📊 Результат: успешно=$SUCCESS, ошибок=$FAILED"

if [ $SUCCESS -gt 0 ]; then
    echo "🔄 Перезагрузка Nginx..."
    nginx -t && systemctl reload nginx
    echo "✅ Nginx перезагружен"
fi

echo "✅ Готово!"

