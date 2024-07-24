#!/bin/bash

# Update package list
sudo apt update

# Install necessary packages
sudo apt install -y apache2 mysql-server php8.2 libapache2-mod-php8.2 php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip composer

# Install Laravel dependencies
sudo composer install

# Configure Apache
sudo a2enmod rewrite
sudo a2ensite 000-default.conf
sudo service apache2 restart

# Configure MySQL
sudo mysql -u root -e "CREATE DATABASE glaciargate;"
sudo mysql -u root -e "GRANT ALL PRIVILEGES ON glaciargate.* TO 'root'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -u root -e "FLUSH PRIVILEGES;"

# Create tables
sudo php artisan make:migration create_users_table
sudo php artisan make:migration create_vms_table
sudo php artisan migrate

# Configure Laravel
sudo cp.env.example.env
sudo nano.env

# Set up Laravel key
sudo php artisan key:generate

# Start Apache
sudo service apache2 start
