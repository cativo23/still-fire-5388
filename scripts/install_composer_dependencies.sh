#!/bin/bash
cd  /home/ubuntu/still-fire
apt install wget -y
wget https://getcomposer.org/composer.phar
php composer.phar update
