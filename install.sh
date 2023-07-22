#!/bin/bash

#set -e 
clear

printf "\n\n ** Install script START **\n"

# Enable Interfaces
#
#  0 - w1-gpio

# Install
#
#  1 - UnZip
#  2 - PHP
#  3 - Python
#  4 - RRD
# ?5 - MySQL ?
# ?6 - MySQL PHP connector ?
# ?7 - MySQL Python connector ?
#  8 - Apache2
#  9 - Apache PHP library
# 10 - OffGrid web app
# 11 - OffGrid cron jobs
# 12 - Configure Apache



#  1 - UnZip

UNZIP_INSTALLED=$(which unzip)
if [[ "$UNZIP_INSTALLED" == "" ]]
then
  printf "\n\n Installing UNZIP tool ...\n"
  # Install Apache
  apt-get install unzip -y

  UNZIP_INSTALLED=$(find /var/lib/dpkg -name unzip*)
    if [[ "$UNZIP_INSTALLED" == "" ]]
    then
      printf "\n\n EXITING : UNZIP tool installation FAILED\n"
      exit 1
    fi
else
  printf "\n\n UNZIP tool is already installed. \n"
fi

#  2 - PHP

PHP_INSTALLED=$(which php)
if [[ "$PHP_INSTALLED" == "" ]]
then
  printf "\n\n Installing PHP ...\n"

  apt-get install php -y

  PHP_INSTALLED=$(which php)
    if [[ "$PHP_INSTALLED" == "" ]]
    then
      printf "\n\n EXITING : PHP installation FAILED\n"
      exit 1
    fi
else
  PHP_VERSION=$(php -v | sed -n '1p')
  printf "\n\n PHP is already installed. $(which php) $PHP_VERSION \n"
fi

#  3 - Python

PYTHON_INSTALLED=$(which python)
if [[ "$PYTHON_INSTALLED" == "" ]]
then
  printf "\n\n Installing Python ...\n"

  apt-get install python -y

  PYTHON_INSTALLED=$(which python)
    if [[ "$PYTHON_INSTALLED" == "" ]]
    then
      printf "\n\n EXITING : Python installation FAILED\n"
      exit 1
    fi
else
  PYTHON_VERSION=$(python --version)
  printf "\n\n Python is already installed. $(which python) $PYTHON_VERSION \n"
fi

#  4 - RRD

RRD_INSTALLED=$(find /var/lib/dpkg -name rrdtool*)
if [[ "$RRD_INSTALLED" == "" ]]
then
  printf "\n\n Installing RRD tool ...\n"

  apt-get install rrdtool php-rrd -y

  RRD_INSTALLED=$(find /var/lib/dpkg -name rrdtool*)
    if [[ "$RRD_INSTALLED" == "" ]]
    then
      printf "\n\n EXITING : RRD tool installation FAILED\n"
      exit 1
    fi
else
  RRD_VERSION=$(rrdtool -v | sed -n '1p')
  printf "\n\n RRD tool is already installed. $(which rrdtool) $RRD_VERSION \n"
fi

# ?5 - MySQL ?

#MYSQL_INSTALLED=$(which mysql)
#if [[ "$MYSQL_INSTALLED" == "" ]]
#then
#  printf "\n\n Installing MYSQL ...\n"
#  # Install Apache
#  apt-get install mariadb-server -y --fix-missing

#  MYSQL_INSTALLED=$(which mysql)
#    if [[ "$MYSQL_INSTALLED" == "" ]]
#    then
#      printf "\n\n EXITING : MYSQL installation FAILED\n"
#      exit 1
#    fi
#else
#  MYSQL_VERSION=$(mysql --version)
#  printf "\n\n MYSQL is already installed. $(which mysql) $MYSQL_VERSION \n"
#fi

# ?6 - MySQL PHP connector ?
# ?7 - MySQL Python connector ?

#  8 - Apache2

APACHE2_INSTALLED=$(which apache2)
if [[ "$APACHE2_INSTALLED" == "" ]]
then
	printf "\n\nInstalling Apache2 and enabling\n\n"
	apt-get install apache2 -y
	update-rc.d apache2 enable
	a2dissite 000-default.conf
	
else
	APACHE_VERSION=$(apache2 -version | sed -n '1p')
	printf "\n\n Apache is already installed. $(which apache2) $APACHE_VERSION \n"
fi

#  9 - Apache PHP-MOD library

PHPMOD_INSTALLED=$(find /var/lib/dpkg -name libapache2-mod-php*)
if [[ "$PHPMOD_INSTALLED" == "" ]]
then
	printf "\n\nInstalling Apache PHP-MOD\n\n"
	apt-get install libapache2-mod-php -y
	
	PHPMOD_INSTALLED=$($(find /var/lib/dpkg -name libapache2-mod-php*))
    if [[ "$PHPMOD_INSTALLED" == "" ]]
    then
      printf "\n\n EXITING : Apache PHP-MOD installation FAILED\n"
      exit 1
    fi

else
	printf "\n\n Apache PHP-MOD already installed . $(find /var/lib/dpkg -name libapache2-mod-php*.*.list) \n"
fi



# 10 - OffGrid web app

INSTALL_USR=$(ls /home/)

printf "\n\n Install User : $INSTALL_USR\n"
printf " Please choose a new install user: (leave blank to not change) "
read NEW_INSTALL_USR

if [[ "$NEW_INSTALL_USR" = "" ]]
then
  printf " Install user has not been changed.\n"
else
  # Update 
  printf " Changing insall user from $INSTALL_USR to $NEW_INSTALL_USR"
  INSTALL_USR = NEW_INSTALL_USR
fi


INSTALL_DIR="/home/"$INSTALL_USR

printf "\n\n Home directory : $INSTALL_DIR\n"
printf " Please choose a new install directory: (leave blank to not change) "
read NEW_INSTALL_DIR

if [[ "$NEW_INSTALL_DIR" = "" ]]
then
  printf " Install directory has not been changed.\n"
else
  # Update 
  printf " Changing insatt directory from $INSTALL_DIR to $NEW_INSTALL_DIR"
  INSTALL_DIR = NEW_INSTALL_DIR
fi

if [ ! -f $INSTALL_DIR"/offgrid/README.md" ]
then
	printf "\n\n Installing OffGrid ...\n"

	cd $INSTALL_DIR
	if [ -d $INSTALL_DIR"/offgrid" ]
	then
		rm -rf $INSTALL_DIR"/offgrid"
	fi
  
	if [ -d "/var/www/offgrid" ]
	then
		rm -rf "/var/www/offgrid"
	fi
  
	wget "https://github.com/JeffreyPowell/offgrid/archive/master.zip" -O $INSTALL_DIR"/offgrid.zip"
  
  if [ ! -f $INSTALL_DIR"/offgrid.zip"]:
    echo $INSTALL_DIR"/offgrid.zip file not found !"
    exit()

	/usr/bin/unzip $INSTALL_DIR"/offgrid.zip"
  
  exit()

	rm -rf $INSTALL_DIR"/offgrid.zip"
  
	mv $INSTALL_DIR"/offgrid-master" $INSTALL_DIR"/offgrid"
  
	mv $INSTALL_DIR"/offgrid/www" "/var/www/offgrid"
  
	if [ ! -d $INSTALL_DIR"/offgrid/data" ]
	then
		mkdir $INSTALL_DIR"/offgrid/data"
	fi
  
	chown -R $INSTALL_USR:$INSTALL_USR $INSTALL_DIR"/offgrid"
	chmod -R 755 $INSTALL_DIR"/offgrid"
	# chown -R pi:pi "/home/pi/offgrid/configs"
	# chmod -R 755 "/home/pi/offgrid/configs"
	
	chown -R $INSTALL_USR:www-data "/var/www/offgrid"
	chmod -R 775 "/var/www/offgrid"
  
	if [ ! -f $INSTALL_DIR"/offgrid/README.md" ]
	then
		printf "\n\n EXITING : OffGrid installation FAILED\n"
		exit 1
	fi
else
	printf "\n\n OffGrid is already installed. \n"
fi

# 11 - OffGrid cron jobs


if [ ! -f "/etc/cron.d/offgrid" ]
then
	cat > /etc/cron.d/offgrid <<CRON
* * * * * $INSTALL_USR /usr/bin/python $INSTALL_DIR/offgrid/cron/poll-adc.py
CRON
	service cron restart
fi
  



# 12 - Configure Apache

#if [ ! -f "/etc/apache2/sites-available/offgrid.conf" ]
#then
	printf "\n\nConfiguring Apache offgrid site\n\n"

	cat > /etc/apache2/sites-available/offgrid.conf << VIRTUALHOST
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	DocumentRoot /var/www/offgrid
  <Directory /var/www/offgrid/>
        Options -Indexes
        AllowOverride all
        Order allow,deny
        allow from all
  </Directory>
	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
VIRTUALHOST

	cat > /var/www/offgrid/index.htm << WEBSITE
<h1>OffGrid</h1>
WEBSITE

	a2ensite offgrid.conf
	a2dissite 000-default
	systemctl restart apache2
#fi	

printf "\n\n ** Install script END **\n"
