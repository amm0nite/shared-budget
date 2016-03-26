# Shared budget

## Development

Get the code with `git clone git@github.com:amm0nite/shared-budget`.

Build the container :

```
cd docker/webapp/
bash build.sh
```

Launch containers with `docker-compose up`.

Install dependencies :

```
composer install
bower install
```

## Installation

On a new Debian or Ubuntu install,

Clone in `$HOME/budget`

```
cd $HOME
mkdir budget
cd budget
git clone git@github.com:amm0nite/shared-budget.git .
```

Install packages `apt-get install apache2 php5 php5-mysql mysql-server mysql-client`

Create database and its user :

```sql
CREATE DATABASE budget;
CREATE USER 'budget'@'localhost' IDENTIFIED BY 'mypass';
GRANT ALL PRIVILEGES ON budget.* TO 'budget'@'localhost';
```

Install composer and do `composer install`

Setup the database schema :

```
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
```

Change permissions :

```
chown -Rv www-data:www-data var/cache
chown -Rv www-data:www-data var/logs
```

Install nodejs for npm 

Install bower golbally `npm install -g bower`

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
