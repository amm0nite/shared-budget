# Shared budget

## Development

Get the code with `git clone git@github.com:amm0nite/shared-budget`

Build the container

```bash
cd docker/php-shared-budget/
bash build.sh
```

Launch containers

```bash
cd docker/
docker-compose up
```

Go inside the container with `docker exec -it php-shared-budget bash`

```bash
# Install dependencies
composer install
bower install --allow-root
# Setup permissions
chown -Rv www-data:www-data var/cache
chown -Rv www-data:www-data var/logs
# Create database schema
php bin/console doctrine:schema:create
```

Edit `web/app_dev.php` to remove access restrictions

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
apt-get install	php php-mysql
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

Enable the apache2 mod rewrite `a2enmod rewrite`

Restart apache2 `service apache2 restart`
