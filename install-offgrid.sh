#!/bin/bash

#set -e 
clear

if [[ `whoami` != "root" ]]
then
  printf "\n\n Script must be run as root. \n\n"
  exit 1
fi


ENABLE_W1=$( cat /boot/config.txt | grep '^dtoverlay=w1-gpio$' )
if [[ $ENABLE_W1 == "" ]]
then
  echo "dtoverlay=w1-gpio" >> /boot/config.txt
  
  ENABLE_W1=$( cat /boot/config.txt | grep '^dtoverlay=w1-gpio$' )
  if [[ $ENABLE_W1 == "" ]]
  then  
    printf "\n\n EXITING : Unable to write to boot config. \n\n"
    exit 1
  fi
  apt-get update -y
  printf "\n\n REBOOT : Reeboot required to enable one wire module.\n\n"
  shutdown -r +1
else
  printf "\n One wire module enabled. \n"
  
  modprobe w1-gpio
  modprobe w1-therm
  
  printf "\n w1_gpio and w1_therm modules enabled. \n"
fi


RRD_INSTALLED=$(find /var/lib/dpkg -name rrdtool*)
if [[ "$RRD_INSTALLED" == "" ]]
then
  printf "\n\n Installing RRD tool ...\n"
  # Install Apache
  apt-get install rrdtool php-rrd -y

  RRD_INSTALLED=$(find /var/lib/dpkg -name rrdtool*)
    if [[ "$RRD_INSTALLED" == "" ]]
    then
      printf "\n\n EXITING : RRD tool installation FAILED\n"
      exit 1
    fi
else
  printf "\n\n RRD tool is already installed. \n"
fi


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

APACHE2_INSTALLED=$(which apache2)
if [[ "$APACHE2_INSTALLED" == "" ]]
then
	printf "\n\nInstalling Apache2\n\n"
	apt-get install apache2 -y
	update-rc.d apache2 enable
	a2dissite 000-default.conf
	
else
	printf "\nApache2 already installed ... skipping ...\n"
fi

if [ ! -d "/var/www/offgrid" ]
	then
	      mkdir "/var/www/offgrid"
	
	
	cat > /etc/apache2/sites-available/offgrid.conf << VIRTUALHOST
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	DocumentRoot /var/www/offgrid
	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
VIRTUALHOST

	cat > /var/www/offgrid/index.html << WEBSITE
<h1>OffGrid</h1>
WEBSITE

	a2ensite offgrid.conf
	service apache2 restart
fi	


if [ ! -f "/home/pi/offgrid/README.md" ]
then
  printf "\n\n Installing OffGrid ...\n"
  # Install Apache
  cd /home/pi
  if [ -d "/home/pi/offgrid" ]
  then
    rm -rf "/home/pi/offgrid"
  fi
  
  if [ -d "/var/www/offgrid" ]
  then
    rm -rf "/var/www/offgrid"
  fi
  
  wget "https://github.com/JeffreyPowell/offgrid/archive/master.zip" -O "/home/pi/offgrid.zip"
  
  unzip "/home/pi/offgrid.zip" 
  
  rm -rf "/home/pi/offgrid.zip"
  
  mv "/home/pi/offgrid-master" "/home/pi/offgrid"
  
  mv "/home/pi/offgrid/www" "/var/www/offgrid"
  
  if [ ! -d "/home/pi/offgrid/data" ]
  then
    mkdir "/home/pi/offgrid/data"
  fi
  
  chown -R pi:pi "/home/pi/offgrid"
  chmod -R 755 "/home/pi/offgrid"
 # chown -R pi:pi "/home/pi/offgrid/configs"
 # chmod -R 755 "/home/pi/offgrid/configs"
  chown -R pi:pi "/var/www/offgrid"
  chmod -R 755 "/var/www/offgrid"
  
  if [ ! -f "/home/pi/offgrid/README.md" ]
    then
      printf "\n\n EXITING : OffGrid installation FAILED\n"
      exit 1
    fi

  if [ ! -f "/etc/cron.d/offgrid" ]
  then
    cat > /etc/cron.d/offgrid <<CRON
* * * * * pi /usr/bin/python /home/pi/offgrid/cron/poll-adc.py
CRON
    service cron restart
  fi
  
else
  printf "\n\n OffGrid is already installed. \n"
fi

