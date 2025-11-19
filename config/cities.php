<?php

return [
    'default' => 'moscow',
    
    'available' => [
        'moscow' => [
            'code' => 'moscow',
            'name' => 'Москва',
            'domain' => env('APP_DOMAIN', 'prostitutkimoskvytake.org'),
            'subdomain' => null,
        ],
        'spb' => [
            'code' => 'spb',
            'name' => 'Санкт-Петербург',
            'domain' => env('APP_DOMAIN', 'prostitutkimoskvytake.org'),
            'subdomain' => 'spb',
        ],
    ],
    
    'server' => [
        'ip' => env('SERVER_IP', '45.82.66.116'),
        'project_path' => env('PROJECT_PATH', '/var/www/noviysayt/data/www/prostitutkimoskvytake.org'),
        'php_fpm_sock' => env('PHP_FPM_SOCK', '/var/www/php-fpm/3584.sock'),
        'ssl_cert' => env('SSL_CERT', '/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.crtca'),
        'ssl_key' => env('SSL_KEY', '/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.key'),
    ],
];

