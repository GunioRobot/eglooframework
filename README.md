README
======

What is eGloo?
-----------------

eGloo is a modern, extensible, scalable and stable PHP 5.3+ framework proven
in multiple production environments. It is geared towards rapid development,
maximum code reuse and minimal developer overhead.

It serves as the foundation for rapid scaling ecommerce solutions that deliver
lightning-fast performance, and even an entire line of touchscreen solutions
in multiple nations around the world.

eGloo can be used to develop websites, SaaS solutions, IaaS solutions and 
provides built-in support for data source and format agnostic ETL solutions.

Requirements
------------

eGloo 1.0 is only supported on PHP 5.3+.  eGloo 2.0 will be PHP 5.4+ only.

Installation
------------

eGloo makes use of continuous integration to maintain stability and consistent
performance in its master branch.  For that reason, cloning from GitHub is
the best way to install eGloo.

### Ubuntu 10.04 LTS

	sudo apt-get install php-apc php5-imagick php5-mcrypt php5-memcache php5-memcached php5-suhosin php5-cli php5 php5-common php5-dev php5-mysql php5-pgsql php5-sqlite php-soap php-openid php5-odbc php5-gd php5-xmlrpc
	sudo apt-get install apache2
	sudo apt-get install git-core
	sudo add-apt-repository ppa:pitti/postgresql
	sudo apt-get update
	sudo apt-get install postgresql-9.0 postgresql-client-9.0 postgresql-doc-9.0
	sudo apt-get install mysql-server-5.1 mysql-client-5.1

**NOTE: Make sure apache has mod_rewrite enabled and AllowOverride All set (.htaccess allowed)**

After cloning eglooframework:

	cd eglooframework/Build
	sudo ./Install.sh

**Follow all instructions in installer**

To see how to make an eGloo application, please refer to Skeleton.app in the eGloo application directory (set during install)

The webroot that is setup during install contains several files:

	.htaccess // Environment variables and DB connection settings
	Config.xml // Local Configuration Options
	index.php // eGloo Bootloader
	PHP -> eglooframework/PHP // Symlink
	System.xml // eGloo System Configuration Information - **DO NOT MODIFY**

eGloo Installer is "non-destructive".  If your webroot gets hosed, you can rerun the installer and it will not delete any
files/symlinks currently set.  It will move your old .htaccess file to .htaccess-{timestamp}.

Important directories:

Configuration Cache: /var/tmp/com.egloo.cache/

Template Cache: /var/cache/egloo/{Application.app} (Exact directory OS-dependent, check System.xml for specific path)

Hot-caching of CSS, XCSS, JS, XJS and Images happens in the webroot dynamically.  These directories can be safely deleted to
clear static cache and eGloo will repopulate them as needed.

Finally, it is recommended that you: sudo cp ./Scripts/restartmemcache to /usr/bin/restartmemcache or /usr/local/bin/restartmemcache

eGloo, by default, expects several memcache daemons on specific ports on localhost to be made available if memcache is used.
These can be changed by editing eglooframework/PHP/Classes/Performance/Caching/Deprecated/CacheGateway.php and the restartmemcache script

In exchange for rapid development and proper SE design, eGloo sacrifices speed in development mode and ability to run without caching support.

**NOTE: eGloo is NOT meant to be run without the use of APC or memcached.  There are no plans to support running without a caching engine available for production.**

Documentation
-------------

Coming soon.  No, seriously.