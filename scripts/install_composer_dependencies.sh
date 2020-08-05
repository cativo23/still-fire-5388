#!/bin/bash
cd  /home/ubuntu/still-fire
apt install wget -y
wget https://getcomposer.org/composer.phar
php composer.phar update
mkdir node_modules
chown -R ubuntu:ubuntu node_modules
chmod -R a+x node_modules
chown -R ubuntu:ubuntu public
chmod -R a+x public
