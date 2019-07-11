all: start

hello:
	@echo "Hello"

install:
	wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php --
	mv composer.phar composer
	composer install
	docker exec -it laradock_mysql_1 mysql -u"root" -p"root" -e 'create database if not exists coderoom_db'
	docker exec -it laradock_mysql_1 mysql -u"root" -p"root" -e 'alter user "root"@"localhost" identified with mysql_native_password by "root"'
	docker exec -it laradock_mysql_1 mysql -u"root" -p"root" -e 'alter user "root"@"%" identified with mysql_native_password by "root"'
	php artisan
	php artisan key:generate
	php artisan migrate	

start:
	cd laradock; docker-compose up -d apache2 mysql workspace
