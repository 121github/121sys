#!/bin/bash

apache_config_file="/etc/apache2/envvars"
apache_vhost_file="/etc/apache2/sites-available/vagrant_vhost.conf"
php_config_file="/etc/php5/apache2/php.ini"
xdebug_config_file="/etc/php5/mods-available/xdebug.ini"
mysql_config_file="/etc/mysql/my.cnf"
default_apache_index="/vagrant/index.php"
project_web_root="/vagrant" # Local side path to the project content.
project_web_index="index.php" # What file should be read.
project_web_server_admin="webmaster@localhost" # E-mail address for the admin. Fake will do...
project_web_logs_dir="/vagrant/vagrant/logs" # Server side path to logs.

# This function is called at the very bottom of the file
main() {
	repositories_go
	update_go
	network_go
	tools_go
	apache_go
	mysql_go
	php_go
	autoremove_go
	composer_go
#	nodejs_go
#	grunt_go
#	bower_go
#	gulp_go
#	ruby_go
#	sendmail_go
#	mailcatcher_go
#	compass_go

    app_actions_go
}

repositories_go() {
	echo "NOOP"
}

update_go() {
	# Update the server
	echo "Update repository"
	apt-get update
	# apt-get -y upgrade
}

autoremove_go() {
	apt-get -y autoremove
}

network_go() {
	IPADDR=$(/sbin/ifconfig eth0 | awk '/inet / { print $2 }' | sed 's/addr://')
	sed -i "s/^${IPADDR}.*//" /etc/hosts
	echo ${IPADDR} ubuntu.localhost >> /etc/hosts			# Just to quiet down some error messages
}

tools_go() {

    echo "Build Essentials and Other Packages"
    apt-get -y install build-essential binutils-doc python-software-properties python g++ make

    echo "cURL"
    apt-get -y install curl

    echo "Git"
    apt-get -y install git
}

apache_go() {
	# Install Apache
	apt-get -y install apache2 libapache2-mod-php5

#	sed -i "s/^\(.*\)www-data/\1vagrant/g" ${apache_config_file}
#	chown -R vagrant:vagrant ${project_web_logs_dir}

	if [ ! -f "${apache_vhost_file}" ]; then
		cat << EOF > ${apache_vhost_file}
<VirtualHost *:80>
	# Admin email, Server Name (domain name), and any aliases
    ServerAdmin ${project_web_server_admin}
    ServerName localhost

    # Index file and Document Root (where the public files are located)
    DirectoryIndex index.html ${project_web_index}
    DocumentRoot ${project_web_root}

	# Log file locations
	LogLevel debug
    ErrorLog ${project_web_logs_dir}/error.log
    CustomLog ${project_web_logs_dir}/access.log combined

	# Directory specifics & VM workarounds
    <Directory ${project_web_root}>
    	Require all granted
        AllowOverride All
        Order allow,deny
        Allow from all
        EnableSendfile off
        #Options Indexes FollowSymLinks MultiViews
    </Directory>
</VirtualHost>
EOF
	fi

	a2dissite 000-default
	a2ensite vagrant_vhost

	a2enmod rewrite

	service apache2 reload
	update-rc.d apache2 enable
}

php_go() {
	apt-get -y install php5 php5-curl php5-cli php5-dev php5-fpm php5-gd php5-json php5-mcrypt php5-gd php5-imagick php5-mysql php5-sqlite php5-xdebug

	sed -i "s/display_startup_errors = Off/display_startup_errors = On/g" ${php_config_file}
	sed -i "s/display_errors = Off/display_errors = On/g" ${php_config_file}

    #Set the timezone
    sudo sed -i "s/;date.timezone =.*/date.timezone = Europe\/London/" ${php_config_file}

	if [ ! -f "{$xdebug_config_file}" ]; then
		cat << EOF > ${xdebug_config_file}
zend_extension=xdebug.so
xdebug.remote_enable=1
xdebug.remote_connect_back=1
xdebug.remote_port=9000
xdebug.remote_host=10.0.2.2
EOF
	fi

	service apache2 reload

	# Install latest version of Composer globally
	if [ ! -f "/usr/local/bin/composer" ]; then
		curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
	fi

	# Install PHP Unit 4.8 globally
	if [ ! -f "/usr/local/bin/phpunit" ]; then
		curl -O -L https://phar.phpunit.de/phpunit-old.phar
		chmod +x phpunit-old.phar
		mv phpunit-old.phar /usr/local/bin/phpunit
	fi
}

mysql_go() {
	# Install MySQL
	echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
	echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections
	apt-get -y install mysql-client mysql-server

	sed -i "s/bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/" ${mysql_config_file}

	# Allow root access from any host
	echo "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION" | mysql -u root --password=root
	echo "GRANT PROXY ON ''@'' TO 'root'@'%' WITH GRANT OPTION" | mysql -u root --password=root

	if [ -d "/vagrant/vagrant/provision-sql" ]; then
		echo "Executing all SQL files in /vagrant/vagrant/provision-sql folder ..."
		echo "-------------------------------------"
		for sql_file in /vagrant/vagrant/provision-sql/*.sql
		do
			echo "EXECUTING $sql_file..."
	  		time mysql -u root --password=root < $sql_file
	  		echo "FINISHED $sql_file"
	  		echo ""
		done
	fi

	service mysql restart
	update-rc.d apache2 enable
}

composer_go() {
    echo "Composer | Install composer"
    curl -sS https://getcomposer.org/installer | /usr/bin/php && /bin/mv -f /home/vagrant/composer.phar /usr/local/bin/composer
    creates=/usr/local/bin/composer

    echo "Composer | Update Composer"
    /usr/local/bin/composer self-update
}

nodejs_go() {
    echo "NodeJS | Install NodeJS"
    sudo apt-get -y install nodejs npm
}

grunt_go() {
    echo "Grunt | Install grunt"
    npm install grunt-cli -g
}

bower_go() {
    echo "Bower | Install bower"
    npm install bower -g
}

gulp_go() {
    echo "Gulp | Install gulp"
    npm install gulp -g
}

ruby_go() {
    echo "Ruby | Adding the GPG keys beforehand"
#    gpg --keyserver hkp://keys.gnupg.net --recv-keys D39DC0E3
#
#    echo "Ruby | RVM Installation of Ruby"
}

sendmail_go() {
    echo "Sendmail | Install Sendmail packages"
    apt-get -y install sendmail
}

mailcatcher_go() {
    echo "Mailcatcher | Install Mailcatcher dependencies"
    apt-get -y install libsqlite3-dev

#    echo "Mailcatcher | Install Mailcatcher itself"
#    gem install mailcatcher creates=/usr/local/bin/catchmail
#
#    echo "Mailcatcher | Let's enable Mailcatcher in php"
}

memcache_go() {
    echo "Memcache | Install the Memcache"
    apt-get -y install php5-memcache memcached
}

compass_go() {
    echo "Compass | Install SASS"
    gem install sass creates=/usr/local/bin/sass

    echo "Compass | Install Susy"
    gem install susy

    echo "Compass | Install Compass"
    gem install compass creates=/usr/local/bin/compass

#    echo "Compass | Check the project path"
#    cat ${compass_project}/config.rb
#
#    echo "Compass | Set the watch worker"

}

app_actions_go() {
    echo "Access to the app path"
    cd /vagrant/

    echo "Composer | Update"
    composer update;
}
main
exit 0