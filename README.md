![PHP Powered](http://php.net/images/logos/php-power-micro2.png "PHP Powered")

# Shared budget

## Development

Get the code with `git clone git@github.com:amm0nite/shared-budget`

Requirements
 - Docker + docker-compose
 - php7 + composer
 - node + npm

Launch containers

```bash
cd docker/
docker-compose up
```

Install dependencies

```bash
composer install
npm install
```

Create the database schema

```bash
php bin/console doctrine:schema:create
```

Start the built-in web server
```bash
php bin/console server:start
```

## Installation

On a new Debian or Ubuntu install,

Clone in `$HOME/budget`

```bash
cd $HOME
mkdir budget
cd budget
git clone https://github.com/amm0nite/shared-budget.git .
```

Install the required packages 
```bash
apt-get install apache2
apt-get install	php php-mysql php-xml
apt-get install libapache2-mod-php
apt-get install	mysql-server mysql-client
```

Create database and its user :

```sql
CREATE DATABASE budget;
CREATE USER 'budget'@'localhost' IDENTIFIED BY 'mypass';
GRANT ALL PRIVILEGES ON budget.* TO 'budget'@'localhost';
```

Install composer and do `composer install`

Setup the database schema :

```bash
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
```

Change permissions :

```bash
chown -Rv www-data:www-data var/cache
chown -Rv www-data:www-data var/logs
```

Install nodejs to get npm

Install bower `npm install -g bower`

Install frontend dependencies with bower `bower install`

Create virtualhost `budget.conf` in `/etc/apache2/sites-available`:

```apache
<VirtualHost *:80>
	ServerName budget.example.com

	ServerAdmin webmaster@localhost
	DocumentRoot /home/username/budget/web
	
	<Directory /home/username/budget/web/>
		AllowOverride All
		Require all granted
		Options -Indexes
	</Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

(See https://symfony.com/doc/current/setup/web_server_configuration.html)

Enable the apache2 mod rewrite `a2enmod rewrite`

Restart apache2 `service apache2 restart`
