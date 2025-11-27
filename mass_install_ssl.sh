#!/bin/bash

echo "=== Массовая установка SSL для всех поддоменов ==="
echo ""

DOMAIN="prostitutkimoskvytake.org"
EMAIL="webmaster@${DOMAIN}"

SUBDOMAINS=(
    "anapa"
    "astrahan"
    "barnaul"
    "belgorod"
    "bryansk"
    "nov"
    "vladivostok"
    "vladimir"
    "volgograd"
    "voronej"
    "gelendjik"
    "ekb"
    "izhevsk"
    "irkutsk"
    "kazan"
    "kirov"
    "krasnodar"
    "krasnoyarsk"
    "kursk"
    "lipetsk"
    "novokuznetsk"
    "novosibyrsk"
    "omsk"
    "orenburg"
    "penza"
    "perm"
    "podolsk"
    "rostov"
    "ryazan"
    "samara"
    "spb"
    "saratov"
    "smolensk"
    "sochi"
    "stavropol"
    "tollyaty"
    "tomsk"
    "tula"
    "tyumen"
    "uyalnovsk"
    "ufa"
    "habarovsk"
    "chelyabinsk"
    "yaroslavl"
)

DOMAIN_LIST="-d ${DOMAIN} -d www.${DOMAIN}"

for subdomain in "${SUBDOMAINS[@]}"; do
    DOMAIN_LIST="${DOMAIN_LIST} -d ${subdomain}.${DOMAIN} -d www.${subdomain}.${DOMAIN}"
done

echo "Будут добавлены следующие домены:"
echo "${DOMAIN_LIST}"
echo ""
echo "Всего доменов: $((${#SUBDOMAINS[@]} * 2 + 2))"
echo ""

read -p "Продолжить? (y/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Отменено"
    exit 1
fi

echo ""
echo "Запуск certbot..."
echo ""

certbot certonly \
    --nginx \
    --non-interactive \
    --agree-tos \
    --email "${EMAIL}" \
    --expand \
    ${DOMAIN_LIST}

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ SSL сертификаты успешно установлены!"
    echo ""
    echo "Перезагрузка Nginx..."
    systemctl reload nginx
    echo "✅ Nginx перезагружен"
    echo ""
    echo "Проверьте любой поддомен:"
    echo "https://spb.${DOMAIN}"
    echo "https://kazan.${DOMAIN}"
else
    echo ""
    echo "❌ Ошибка установки SSL"
    echo "Проверьте логи: /var/log/letsencrypt/letsencrypt.log"
fi

