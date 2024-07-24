#!/bin/bash

# Update package list
sudo apt update

# Install necessary packages
sudo apt install -y apache2 mariadb-server php8.2 libapache2-mod-php8.2 php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip composer git

# Configure Apache
sudo a2enmod rewrite
sudo a2ensite 000-default.conf
sudo service apache2 restart

# Configure MariaDB
sudo mysql -u root -e "CREATE DATABASE glaciargate;"
sudo mysql -u root -e "GRANT ALL PRIVILEGES ON glaciargate.* TO 'root'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -u root -e "FLUSH PRIVILEGES;"

# Create a new user for the dev environment
sudo useradd -m -s /bin/bash devuser
sudo usermod -aG sudo devuser

# Set up SSH keys for the dev user
sudo mkdir -p /home/devuser/.ssh
sudo touch /home/devuser/.ssh/authorized_keys
sudo chown -R devuser:devuser /home/devuser/.ssh

# Configure the firewall to allow incoming SSH connections
sudo ufw allow ssh
sudo ufw enable

# Install and configure Git
sudo apt install -y git
sudo git config --global user.name "Dev User"
sudo git config --global user.email "devuser@example.com"

# Clone the repository
sudo git clone https://github.com/your-repo/glaciargate.git /var/www/glaciargate

# Set up the Laravel environment
sudo cp /var/www/glaciargate/.env.example /var/www/glaciargate/.env
sudo nano /var/www/glaciargate/.env

# Set up the Laravel key
sudo php /var/www/glaciargate/artisan key:generate

# Set up the Laravel migrations
sudo php /var/www/glaciargate/artisan migrate

# Set up the Apache configuration
sudo nano /etc/apache2/sites-available/000-default.conf

# Restart Apache
sudo service apache2 restart
