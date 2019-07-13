all: start

hello:
	@echo "Hello"

install:
	cd laradock; docker-compose up -d apache2 mysql workspace
	cd laradock; cp env-example .env
	curl -sS https://getcomposer.org/installer |php
	sudo mv composer.phar /usr/local/bin/composer
	composer install
	docker exec -it laradock_mysql_1 mysql -u"root" -p"root" -e 'create database if not exists coderoom_db'
	docker exec -it laradock_mysql_1 mysql -u"root" -p"root" -e 'alter user "root"@"localhost" identified with mysql_native_password by "root"'
	docker exec -it laradock_mysql_1 mysql -u"root" -p"root" -e 'alter user "root"@"%" identified with mysql_native_password by "root"'
	php artisan
	php artisan key:generate
	php artisan migrate	

start:
	cd laradock; docker-compose up -d apache2 mysql workspace
