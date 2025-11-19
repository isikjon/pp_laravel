<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CreateCityCommand extends Command
{
    protected $signature = 'city:create {code} {name} {domain?}';
    
    protected $description = 'Создает новый город: таблицы БД, Nginx конфиг, настройки';

    public function handle()
    {
        $cityCode = $this->argument('code');
        $cityName = $this->argument('name');
        $domain = $this->argument('domain') ?: config('app.domain', 'prostitutkimoskvytake.org');
        
        $this->info("Создание города: {$cityName} (код: {$cityCode})");
        
        $this->createDatabaseTables($cityCode);
        
        $this->createNginxConfig($cityCode, $domain);
        
        $this->createHomePageSettings($cityCode, $cityName);
        
        $this->info("\n✅ Город {$cityName} успешно создан!");
        $this->info("\nСледующие шаги:");
        $this->info("1. Создайте A-запись в DNS: {$cityCode}.{$domain} → IP сервера");
        $this->info("2. Скопируйте конфиг: sudo cp storage/nginx/{$cityCode}.{$domain}.conf /etc/nginx/vhosts/noviysayt/");
        $this->info("3. Создайте директорию: sudo mkdir -p /etc/nginx/vhosts-resources/{$cityCode}.{$domain}/dynamic");
        $this->info("4. Проверьте Nginx: sudo nginx -t");
        $this->info("5. Перезапустите Nginx: sudo systemctl reload nginx");
        $this->info("6. Получите SSL: sudo certbot certonly --nginx --expand -d {$cityCode}.{$domain}");
        
        return 0;
    }
    
    protected function createDatabaseTables($cityCode)
    {
        $this->info("\n📊 Создание таблиц в БД...");
        
        $tables = [
            'girls' => $this->getGirlsTableStructure(),
            'masseuses' => $this->getMasseusesTableStructure(),
        ];
        
        foreach ($tables as $baseTable => $structure) {
            $tableName = "{$baseTable}_{$cityCode}";
            
            if (Schema::hasTable($tableName)) {
                $this->warn("  ⚠ Таблица {$tableName} уже существует, пропускаем");
                continue;
            }
            
            DB::statement($structure($tableName));
            $this->info("  ✓ Создана таблица: {$tableName}");
        }
    }
    
    protected function getGirlsTableStructure()
    {
        return function($tableName) {
            return "CREATE TABLE `{$tableName}` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `anketa_id` TEXT NOT NULL UNIQUE,
                `name` TEXT NOT NULL,
                `age` TEXT,
                `height` INTEGER,
                `weight` INTEGER,
                `breast_size` INTEGER,
                `metro` TEXT,
                `district` TEXT,
                `city` TEXT,
                `phone` TEXT,
                `whatsapp` TEXT,
                `telegram` TEXT,
                `coordinates` TEXT,
                `services` TEXT,
                `tariffs` TEXT,
                `meeting_places` TEXT,
                `media_images` TEXT,
                `about` TEXT,
                `hair_color` TEXT,
                `pubic_hair` TEXT,
                `ethnicity` TEXT,
                `rating` REAL DEFAULT 0,
                `views_count` INTEGER DEFAULT 0,
                `is_verified` INTEGER DEFAULT 0,
                `is_premium` INTEGER DEFAULT 0,
                `sort_order` INTEGER DEFAULT 0,
                `created_at` TEXT,
                `updated_at` TEXT
            )";
        };
    }
    
    protected function getMasseusesTableStructure()
    {
        return function($tableName) {
            return "CREATE TABLE `{$tableName}` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `anketa_id` TEXT NOT NULL UNIQUE,
                `name` TEXT NOT NULL,
                `age` TEXT,
                `height` INTEGER,
                `weight` INTEGER,
                `breast_size` INTEGER,
                `metro` TEXT,
                `district` TEXT,
                `city` TEXT,
                `phone` TEXT,
                `whatsapp` TEXT,
                `telegram` TEXT,
                `coordinates` TEXT,
                `services` TEXT,
                `tariffs` TEXT,
                `meeting_places` TEXT,
                `media_images` TEXT,
                `about` TEXT,
                `massage_types` TEXT,
                `rating` REAL DEFAULT 0,
                `views_count` INTEGER DEFAULT 0,
                `is_verified` INTEGER DEFAULT 0,
                `is_premium` INTEGER DEFAULT 0,
                `sort_order` INTEGER DEFAULT 0,
                `created_at` TEXT,
                `updated_at` TEXT
            )";
        };
    }
    
    protected function createNginxConfig($cityCode, $domain)
    {
        $this->info("\n🔧 Генерация Nginx конфига...");
        
        $subdomain = "{$cityCode}.{$domain}";
        $projectPath = env('PROJECT_PATH', '/var/www/noviysayt/data/www/prostitutkimoskvytake.org');
        $serverIp = env('SERVER_IP', '45.82.66.116');
        $phpFpmSock = env('PHP_FPM_SOCK', '/var/www/php-fpm/3584.sock');
        $sslCert = env('SSL_CERT', '/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.crtca');
        $sslKey = env('SSL_KEY', '/var/www/httpd-cert/noviysayt/prostitutkimoskvytake.org_le1.key');
        
        $config = $this->generateNginxConfig([
            'subdomain' => $subdomain,
            'city_code' => $cityCode,
            'project_path' => $projectPath,
            'server_ip' => $serverIp,
            'php_fpm_sock' => $phpFpmSock,
            'ssl_cert' => $sslCert,
            'ssl_key' => $sslKey,
        ]);
        
        $configDir = storage_path('nginx');
        if (!File::exists($configDir)) {
            File::makeDirectory($configDir, 0755, true);
        }
        
        $configFile = "{$configDir}/{$subdomain}.conf";
        File::put($configFile, $config);
        
        $this->info("  ✓ Конфиг создан: {$configFile}");
    }
    
    protected function generateNginxConfig($params)
    {
        extract($params);
        
        return <<<NGINX
server {
	server_name {$subdomain} www.{$subdomain};
	charset off;
	index index.php index.html;
	disable_symlinks if_not_owner from=\$root_path;
	include /etc/nginx/vhosts-includes/*.conf;
	include /etc/nginx/vhosts-resources/{$subdomain}/*.conf;
	include /etc/nginx/users-resources/noviysayt/*.conf;
	access_log /var/www/httpd-logs/{$subdomain}.access.log;
	error_log /var/www/httpd-logs/{$subdomain}.error.log notice;
	ssi on;
	set \$root_path {$project_path}/public;
	root \$root_path;
	listen {$server_ip}:80;
	gzip on;
	gzip_comp_level 5;
	gzip_disable "msie6";
	gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript application/javascript image/svg+xml;
	
	location / {
		try_files \$uri \$uri/ /index.php?\$query_string;
		
		location ~ [^/]\.ph(p\d*|tml)$ {
			try_files /does_not_exists @php;
		}
		
		location ~* ^.+\.(jpg|jpeg|gif|png|svg|js|css|mp3|ogg|mpe?g|avi|zip|gz|bz2?|rar|swf|webp|woff|woff2)$ {
			expires 24h;
		}
	}
	
	location @php {
		include /etc/nginx/vhosts-resources/{$subdomain}/dynamic/*.conf;
		fastcgi_index index.php;
		fastcgi_param PHP_ADMIN_VALUE "sendmail_path = /usr/sbin/sendmail -t -i -f webmaster@{$subdomain}";
		fastcgi_pass unix:{$php_fpm_sock};
		fastcgi_split_path_info ^((?U).+\.ph(?:p\d*|tml))(/?.\$);
		try_files \$uri =404;
		include fastcgi_params;
	}
	
	return 301 https://\$host\$request_uri;
}

server {
	server_name {$subdomain} www.{$subdomain};
	ssl_certificate "{$ssl_cert}";
	ssl_certificate_key "{$ssl_key}";
	ssl_ciphers EECDH:+AES256:-3DES:RSA+AES:!NULL:!RC4;
	ssl_prefer_server_ciphers on;
	ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
	ssl_dhparam /etc/ssl/certs/dhparam4096.pem;
	charset off;
	index index.php index.html;
	disable_symlinks if_not_owner from=\$root_path;
	include /etc/nginx/vhosts-includes/*.conf;
	include /etc/nginx/vhosts-resources/{$subdomain}/*.conf;
	include /etc/nginx/users-resources/noviysayt/*.conf;
	access_log /var/www/httpd-logs/{$subdomain}.access.log;
	error_log /var/www/httpd-logs/{$subdomain}.error.log notice;
	ssi on;
	set \$root_path {$project_path}/public;
	root \$root_path;
	listen {$server_ip}:443 ssl;
	gzip on;
	gzip_comp_level 5;
	gzip_disable "msie6";
	gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript application/javascript image/svg+xml;
	
	add_header X-Frame-Options "SAMEORIGIN";
	add_header X-Content-Type-Options "nosniff";
	add_header Strict-Transport-Security "max-age=31536000;";
	
	location / {
		try_files \$uri \$uri/ /index.php?\$query_string;
		
		location ~ [^/]\.ph(p\d*|tml)$ {
			try_files /does_not_exists @php;
		}
		
		location ~* ^.+\.(jpg|jpeg|gif|png|svg|js|css|mp3|ogg|mpe?g|avi|zip|gz|bz2?|rar|swf|webp|woff|woff2)$ {
			expires 24h;
		}
	}
	
	location ~ /\.(?!well-known).* {
		deny all;
	}
	
	location @php {
		include /etc/nginx/vhosts-resources/{$subdomain}/dynamic/*.conf;
		fastcgi_index index.php;
		fastcgi_param PHP_ADMIN_VALUE "sendmail_path = /usr/sbin/sendmail -t -i -f webmaster@{$subdomain}";
		fastcgi_pass unix:{$php_fpm_sock};
		fastcgi_split_path_info ^((?U).+\.ph(?:p\d*|tml))(/?.\$);
		try_files \$uri =404;
		include fastcgi_params;
	}
}
NGINX;
    }
    
    protected function createHomePageSettings($cityCode, $cityName)
    {
        $this->info("\n⚙️ Создание настроек SEO...");
        
        DB::table('home_page_settings')->insert([
            'city' => $cityCode,
            'title' => "Проститутки {$cityName} - ProstitutkiMoscow",
            'description' => "Каталог анкет с подробными фильтрами и проверенными предложениями в {$cityName}.",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info("  ✓ SEO настройки созданы для города {$cityName}");
    }
}

