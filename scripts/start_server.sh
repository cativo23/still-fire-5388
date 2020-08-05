service apache2 start
chown -R www-data:www-data /home/ubuntu/still-fire #se debe cambiar
find /home/ubuntu/still-fire -type f -exec chmod 644 {} \; #se debe cambiar
find /home/ubuntu/still-fire -type d -exec chmod 755 {} \; #se debe cambiar
cp /home/ubuntu/.env /home/ubuntu/still-fire #se debe cambiar
