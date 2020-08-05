# About
Still Fire implements the JSON:API specification that allows making CRUD operations for Products and Users

# JSON:API
JSON:API is a specification for how a client should request that resources be fetched or modified, and how a server should respond to those requests. 
It is designed to minimize both the number of requests and the amount of data transmitted between clients and servers. This efficiency is achieved without compromising readability, flexibility, or discoverability.

# Prerequisites

The Laravel JSON:API project requires a working Apache/Nginx local environment with PHP, Composer and MySQL.

If you don't already have a local development environment, use one of the following links:
- Windows: [How to install XAMPP on Windows](https://www.apachefriends.org/es/download.html) or [How to install WAMP on Windows](https://updivision.com/blog/post/beginner-s-guide-to-setting-up-your-local-development-environment-on-windows) 
- Linux: [How to install LAMP on Linux](https://howtoubuntu.org/how-to-install-lamp-on-ubuntu) 
- Mac: [How to install MAMP on MAC](https://wpshout.com/quick-guides/how-to-install-mamp-on-your-mac/) 

Install Composer: https://getcomposer.org/doc/00-intro.md

You must have git install if you want to clone the repository

# Installation
## via Github
1. Clone the repository: `git clone https://github.com/cativo23/still-fire-5388.git`
2. Navigate in your newly cloned project: `cd still-fire-5388` 

## via Download
1. Download the repository [here](https://github.com/cativo23/still-fire-5388/archive/master.zip):  
2. Navigate in your newly cloned project: `cd still-fire-5388-master` 

3. Install project dependencies: `composer install`
4. Create a new *.env* file: `cp .env.example .env`
5. Generate application key: `php artisan key:generate`
6. Create a new database in MySQL: `CREATE DATABASE <db_name>`
7. Create a new user for that database: `CREATE USER 'db_username'@'%' IDENTIFIED BY '<db_password>';`
8. Grant Privileges to database: `GRANT ALL PRIVILEGES ON <db_name>.* TO 'db_username'@'%'` 
6. Modify the *.env* file with the correct `DB_DATABASE`, `DB_USERNAME`and `DB_password`
9. Create users table: `php artisan migrate --seed`
10. Add your own mail configurations, to use Google SMTP, click [here](https://medium.com/@agavitalis/how-to-send-an-email-in-laravel-using-gmail-smtp-server-53d962f01a0c)
11. Run `php artisan passport:install`
12. Run `php artisan serve`
