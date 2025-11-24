#!/bin/bash

WEBROOT="/var/www/noviysayt/data/www/prostitutkimoskvytake.org/public"
TEST_DOMAIN="prostitutkimoskvytake.org"

echo "🔍 Диагностика SSL готовности"
echo "================================"

echo ""
echo "1️⃣ Проверка webroot папки:"
echo "   Путь: $WEBROOT"
if [ -d "$WEBROOT" ]; then
    echo "   ✅ Папка существует"
    ls -la "$WEBROOT" | head -10
else
    echo "   ❌ Папка НЕ существует"
fi

echo ""
echo "2️⃣ Создание тестового файла для проверки:"
ACME_DIR="$WEBROOT/.well-known/acme-challenge"
mkdir -p "$ACME_DIR"
echo "test123" > "$ACME_DIR/test-file.txt"
chmod -R 755 "$WEBROOT/.well-known"
echo "   ✅ Создан: $ACME_DIR/test-file.txt"

echo ""
echo "3️⃣ Проверка доступности через HTTP:"
URL="http://$TEST_DOMAIN/.well-known/acme-challenge/test-file.txt"
echo "   URL: $URL"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$URL" 2>/dev/null)
echo "   HTTP код: $RESPONSE"

if [ "$RESPONSE" = "200" ]; then
    CONTENT=$(curl -s "$URL")
    if [ "$CONTENT" = "test123" ]; then
        echo "   ✅ Файл доступен через HTTP и содержимое корректно"
    else
        echo "   ⚠️  Файл доступен, но содержимое некорректно: $CONTENT"
    fi
else
    echo "   ❌ Файл недоступен через HTTP (код: $RESPONSE)"
fi

echo ""
echo "4️⃣ Проверка nginx конфига для домена:"
NGINX_CONFIG="/etc/nginx/vhosts/noviysayt/$TEST_DOMAIN.conf"
if [ -f "$NGINX_CONFIG" ]; then
    echo "   ✅ Конфиг найден: $NGINX_CONFIG"
    echo ""
    echo "   📄 Содержимое конфига:"
    cat "$NGINX_CONFIG"
else
    echo "   ❌ Конфиг НЕ найден"
fi

echo ""
echo "5️⃣ Проверка DNS:"
echo "   Резолв $TEST_DOMAIN:"
dig +short "$TEST_DOMAIN" | head -3

echo ""
echo "================================"
echo "6️⃣ Рекомендации:"
echo ""

if [ "$RESPONSE" != "200" ]; then
    echo "❌ Основная проблема: webroot недоступен через HTTP"
    echo ""
    echo "Добавь в nginx конфиг для $TEST_DOMAIN:"
    echo ""
    echo "location ^~ /.well-known/acme-challenge/ {"
    echo "    root $WEBROOT;"
    echo "    allow all;"
    echo "}"
    echo ""
    echo "После добавления выполни: nginx -t && systemctl reload nginx"
else
    echo "✅ Все готово для установки SSL"
    echo ""
    echo "Команда для установки:"
    echo "certbot certonly --webroot -w $WEBROOT -d $TEST_DOMAIN --email admin@$TEST_DOMAIN --agree-tos --non-interactive"
fi

rm -f "$ACME_DIR/test-file.txt"

