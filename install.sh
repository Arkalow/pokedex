#!/bin/bash

apt install -y sudo nginx php-fpm sqlite3 php-sqlite3 php-zip unzip php-xml git

cd /var/www/html

# composer
php -r copy('https://getcomposer.org/installer', 'composer-setup.php');
php composer-setup.php
php -r unlink('composer-setup.php');
mv composer.phar /usr/local/bin/composer
git clone "https://git.arkalo.ovh/Omer/project.git" /var/www/html/webapp


cd webapp

composer install

cp .env.dist .env

php bin/console d:d:c

php bin/console d:m:m

cat nginx.config > /etc/nginx/sites-available/default

systemctl restart nginx

chown www-data:www-data -R /var/www/html/webapp/
