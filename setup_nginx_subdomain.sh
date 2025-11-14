#!/bin/bash

echo "=== Настройка поддомена spb.prostitutkitest.com ==="

NGINX_SITES_AVAILABLE="/etc/nginx/sites-available"
NGINX_SITES_ENABLED="/etc/nginx/sites-enabled"
CONFIG_FILE="nginx_spb_subdomain.conf"
SITE_NAME="spb_prostitutkitest"

echo "1. Копирование конфига nginx..."
sudo cp $CONFIG_FILE $NGINX_SITES_AVAILABLE/$SITE_NAME

echo "2. Создание симлинка в sites-enabled..."
sudo ln -sf $NGINX_SITES_AVAILABLE/$SITE_NAME $NGINX_SITES_ENABLED/$SITE_NAME

echo "3. Проверка конфигурации nginx..."
sudo nginx -t

if [ $? -eq 0 ]; then
    echo "4. Перезагрузка nginx..."
    sudo systemctl reload nginx
    echo "✓ Nginx успешно перезагружен"
else
    echo "✗ Ошибка в конфигурации nginx. Проверьте конфиг вручную."
    exit 1
fi

echo ""
echo "5. Проверка SSL сертификата..."
echo "Если поддомен не включен в сертификат, выполните:"
echo "sudo certbot certonly --nginx -d spb.prostitutkitest.com"
echo ""
echo "Или обновите существующий сертификат:"
echo "sudo certbot certonly --nginx -d prostitutkitest.com -d www.prostitutkitest.com -d spb.prostitutkitest.com"
echo ""
echo "=== Готово! ==="
echo "Проверьте работу поддомена: https://spb.prostitutkitest.com"

