server {
	server_name {{ $subdomain }} www.{{ $subdomain }};
	charset off;
	index index.php index.html;
	disable_symlinks if_not_owner from=$root_path;
	include /etc/nginx/vhosts-includes/*.conf;
	include /etc/nginx/vhosts-resources/{{ $subdomain }}/*.conf;
	include /etc/nginx/users-resources/noviysayt/*.conf;
	access_log /var/www/httpd-logs/{{ $subdomain }}.access.log;
	error_log /var/www/httpd-logs/{{ $subdomain }}.error.log notice;
	ssi on;
	set $root_path {{ $projectPath }}/public;
	root $root_path;
	listen {{ $serverIp }}:80;
	gzip on;
	gzip_comp_level 5;
	gzip_disable "msie6";
	gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript application/javascript image/svg+xml;
	
	location / {
		try_files $uri $uri/ /index.php?$query_string;
		
		location ~ [^/]\.ph(p\d*|tml)$ {
			try_files /does_not_exists @php;
		}
		
		location ~* ^.+\.(jpg|jpeg|gif|png|svg|js|css|mp3|ogg|mpe?g|avi|zip|gz|bz2?|rar|swf|webp|woff|woff2)$ {
			expires 24h;
		}
	}
	
	location @php {
		include /etc/nginx/vhosts-resources/{{ $subdomain }}/dynamic/*.conf;
		fastcgi_index index.php;
		fastcgi_param PHP_ADMIN_VALUE "sendmail_path = /usr/sbin/sendmail -t -i -f webmaster@{{ $subdomain }}";
		fastcgi_pass unix:{{ $phpFpmSock }};
		fastcgi_split_path_info ^((?U).+\.ph(?:p\d*|tml))(/?.+)$;
		try_files $uri =404;
		include fastcgi_params;
	}
	
	return 301 https://$host$request_uri;
}

server {
	server_name {{ $subdomain }} www.{{ $subdomain }};
	ssl_certificate "{{ $sslCert }}";
	ssl_certificate_key "{{ $sslKey }}";
	ssl_ciphers EECDH:+AES256:-3DES:RSA+AES:!NULL:!RC4;
	ssl_prefer_server_ciphers on;
	ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
	ssl_dhparam /etc/ssl/certs/dhparam4096.pem;
	charset off;
	index index.php index.html;
	disable_symlinks if_not_owner from=$root_path;
	include /etc/nginx/vhosts-includes/*.conf;
	include /etc/nginx/vhosts-resources/{{ $subdomain }}/*.conf;
	include /etc/nginx/users-resources/noviysayt/*.conf;
	access_log /var/www/httpd-logs/{{ $subdomain }}.access.log;
	error_log /var/www/httpd-logs/{{ $subdomain }}.error.log notice;
	ssi on;
	set $root_path {{ $projectPath }}/public;
	root $root_path;
	listen {{ $serverIp }}:443 ssl;
	gzip on;
	gzip_comp_level 5;
	gzip_disable "msie6";
	gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript application/javascript image/svg+xml;
	
	add_header X-Frame-Options "SAMEORIGIN";
	add_header X-Content-Type-Options "nosniff";
	add_header Strict-Transport-Security "max-age=31536000;";
	
	location / {
		try_files $uri $uri/ /index.php?$query_string;
		
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
		include /etc/nginx/vhosts-resources/{{ $subdomain }}/dynamic/*.conf;
		fastcgi_index index.php;
		fastcgi_param PHP_ADMIN_VALUE "sendmail_path = /usr/sbin/sendmail -t -i -f webmaster@{{ $subdomain }}";
		fastcgi_pass unix:{{ $phpFpmSock }};
		fastcgi_split_path_info ^((?U).+\.ph(?:p\d*|tml))(/?.+)$;
		try_files $uri =404;
		include fastcgi_params;
	}
}

