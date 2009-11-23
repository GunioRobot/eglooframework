#! /usr/bin/env bash
##! /bin/bash
#
# eGloo Framework Installation Script (OS X)
#
# Script should be chmod 700 and run as root from the working directory
# using the command ./Install.sh
#
# Copyright 2008 eGloo, LLC
# 
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
# 
#        http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#  
# @author George Cooper
# @copyright 2008 eGloo, LLC
# @license http://www.apache.org/licenses/LICENSE-2.0
# @package Build
# @subpackage Installation
# @version 1.0

# Exit the script if any variables specified are unset when accessed
set -o nounset

# Exit the script if any statement returns a non-true value
# This prevents errors from cascading
set -o errexit

# Exit Error Codes
E_XCD=01       # Script can't change directories
E_NOTROOT=02   # Script not run as root

# Script Constants
ROOT_UID=0     # Only users with $UID 0 have root privileges.

# OS Constants
OS_UBUNTU=0
OS_MACOSX=1
OS_WINDOWS=2
OS_WINDOWS_XP_CYGWIN=3

# Current platform
# Default to Debian
DETECTED_PLATFORM=0

# Get our parent directory
PARENT_DIRECTORY=$(_egloo_parent_dir=$(pwd) ; echo "${_egloo_parent_dir%/*}")

# Get our platform
PLATFORM=$(./shtool platform -v -F "%sc (%ac) %st (%at) %sp (%ap)")

case "$PLATFORM" in
	"4.4BSD/Mach3.0 (iX86) Apple Darwin 9.8.0 (i386) Apple Mac OS X 10.5.8 (iX86)" | "kj" )
		echo "Detected Apple Mac OS X"
		DETECTED_PLATFORM=$OS_MACOSX

		# Default Configuration Parameters (OS X)
		DEFAULT_APPLICATIONS="/Library/Application Support/eGloo/Applications"
		DEFAULT_CACHE_DIR="/Library/Caches/eGloo"
		DEFAULT_CONFIG="/Library/Application Support/eGloo/Framework/Configuration"
		DEFAULT_CUBES="/Library/Application Support/eGloo/Cubes"
		DEFAULT_DOCTRINE="/opt/local/lib/php/Doctrine/lib/Doctrine.php"
		DEFAULT_DOCUMENTATION="/Library/Documentation/eGlooFramework"
		DEFAULT_DOCUMENTROOT="/Library/WebServer/eGloo"
		DEFAULT_FRAMEWORKROOT="/Library/Frameworks/eGloo.framework"
		DEFAULT_LOGPATH="/Library/Logs/eGloo"
		DEFAULT_SMARTY="/opt/local/lib/php/Smarty/Smarty.class.php"
		DEFAULT_WEBUSER="_www"
		DEFAULT_WEBGROUP="admin"
	;;
	
	"LSB (AMD64) GNU/Linux 2.9/2.6 (AMD64) Ubuntu 9.04 (AMD64)" | "LSB (AMD64) GNU/Linux 2.9/2.6 (AMD64) Ubuntu 9.10 (AMD64)" )
		echo "Detected Ubuntu Linux"
		DETECTED_PLATFORM=$OS_UBUNTU

		# Default Configuration Parameters (Ubuntu)
		DEFAULT_APPLICATIONS="/usr/lib/egloo/applications"
		DEFAULT_CACHE_DIR="/var/cache/egloo"
		DEFAULT_CONFIG="/etc/egloo/"
		DEFAULT_CUBES="/usr/lib/egloo/cubes"
		DEFAULT_DOCTRINE="/usr/share/php/smarty/doctrine/lib/Doctrine.php"
		DEFAULT_DOCUMENTATION="/usr/share/doc/egloo"
		DEFAULT_DOCUMENTROOT="/var/www/egloo"
		DEFAULT_FRAMEWORKROOT="/usr/lib/eglooframework"
		DEFAULT_LOGPATH="/var/log/egloo"
		DEFAULT_SMARTY="/usr/share/php/smarty/Smarty.class.php"
		DEFAULT_WEBUSER="www-data"
		DEFAULT_WEBGROUP="www-data"
	;;

	"Windows XP (iX86) Cygwin 1.5.25 (i686) Cygwin 1.5.25 (iX86)" )
		echo "Detected Windows XP (Cygwin)"
		DETECTED_PLATFORM=$OS_WINDOWS_XP_CYGWIN

		# Default Configuration Parameters (Windows XP with Cygwin)
		DEFAULT_APPLICATIONS="/usr/lib/egloo/applications"
		DEFAULT_CACHE_DIR="/var/cache/egloo"
		DEFAULT_CONFIG="/etc/egloo/"
		DEFAULT_CUBES="/usr/lib/egloo/cubes"
		DEFAULT_DOCTRINE="/usr/share/php/doctrine/Doctrine.php"
		DEFAULT_DOCUMENTATION="/usr/share/doc/egloo"
		DEFAULT_DOCUMENTROOT="/cygdrive/c/wamp/www/egloo"
		DEFAULT_FRAMEWORKROOT="/usr/lib/eglooframework"
		DEFAULT_LOGPATH="/var/log/egloo"
		DEFAULT_SMARTY="/usr/share/php/smarty/Smarty.class.php"
		DEFAULT_WEBUSER="user"
		DEFAULT_WEBGROUP="mkpasswd"
	;;

	* )
		# Make this an override choice, and fix the linux checks
		echo "No supported OS detected - Using Ubuntu Linux Defaults (Should be override option...)"
		DETECTED_PLATFORM=$OS_UBUNTU

		# Default Configuration Parameters (Ubuntu)
		DEFAULT_APPLICATIONS="/usr/lib/egloo/applications"
		DEFAULT_CACHE_DIR="/var/cache/egloo"
		DEFAULT_CONFIG="/etc/egloo/"
		DEFAULT_CUBES="/usr/lib/egloo/cubes"
		DEFAULT_DOCTRINE="/usr/share/php/smarty/doctrine/lib/Doctrine.php"
		DEFAULT_DOCUMENTATION="/usr/share/doc/egloo"
		DEFAULT_DOCUMENTROOT="/var/www/egloo"
		DEFAULT_FRAMEWORKROOT="/usr/lib/eglooframework"
		DEFAULT_LOGPATH="/var/log/egloo"
		DEFAULT_SMARTY="/usr/share/php/smarty/Smarty.class.php"
		DEFAULT_WEBUSER="www-data"
		DEFAULT_WEBGROUP="www-data"
	;;
esac

# Check for root
if [[ "$UID" -ne "$ROOT_UID" && $DETECTED_PLATFORM -ne $OS_WINDOWS_XP_CYGWIN ]]
then
	echo "***********************************"
	echo "* Must be root to run this script *"
	echo "***********************************"
	echo
	exit $E_NOTROOT
fi

# Temporarily disable errexit check because grep returns non-true on a result we need
set +o errexit
id "$DEFAULT_WEBUSER" &> id.out
WEBUSER_NOT_FOUND=`grep -i -c "no such user" id.out`
rm id.out
set -o errexit

if [ "$WEBUSER_NOT_FOUND" -eq 1 ]
then
	echo "Error: Web user not found"
	exit
else
	WEB_USER=$DEFAULT_WEBUSER
	WEB_GROUP=$DEFAULT_WEBGROUP
fi

# Give the user an explanation of the build process
echo "********************************"
echo "* eGloo Framework Installation *"
echo "********************************"
echo
echo "This installation script is interactive to give the user as much control as possible."
echo "In the future there should be an option for full automation."
echo

echo -n "Continue? [Y/n]: "
read -e CONFIRM_CONTINUE

# Make sure the user is prepared to answer some setup questions
case "$CONFIRM_CONTINUE" in
	"N" | "n" | "No" | "NO" | "no" )
		echo "Installation Aborted"
		exit
	;;

	* )
	;;
esac

echo -n "Use symlinks to distribution instead of files? (Useful for eGloo Development) [Y/n]: "
read -e USE_SYMLINKS

# Make sure the user is prepared to answer some setup questions
case "$USE_SYMLINKS" in
	"N" | "n" | "No" | "NO" | "no" )
		echo "Using files"
		USE_SYMLINKS=false
	;;

	* )
		echo "Using symlinks"
		USE_SYMLINKS=true
	;;
esac

echo
echo "*****************************"
echo "* eGloo Configuration Files *"
echo "*****************************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_CONFIG\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own configuration path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own configuration path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e CONFIG_PATH
			echo
			echo "Location: \"$CONFIG_PATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done		
	;;

	# User chose the default path
	* ) CONFIG_PATH=$DEFAULT_CONFIG
	;;
esac

echo "Building configuration files..."
echo "\"$CONFIG_PATH\""

if [ $USE_SYMLINKS ]
then
	echo
	# mkdir -p "$CONFIG_PATH"
	# cp -R "../Configuration/Smarty" "$CONFIG_PATH"
else
	mkdir -p "$CONFIG_PATH"
	chown $WEB_USER:$WEB_GROUP "$CONFIG_PATH"
	chmod 755 "$CONFIG_PATH"
	cp -R "../Configuration/Smarty" "$CONFIG_PATH"
fi

echo
echo "********************"
echo "* eGloo Cache Path *"
echo "********************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_CACHE_DIR\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own cache path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own cache path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e CACHE_PATH
			echo
			echo "Location: \"$CACHE_PATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done
	;;

	# User chose the default path
	* ) CACHE_PATH=$DEFAULT_CACHE_DIR
	;;
esac

echo "Building cache path..."
echo "\"$CACHE_PATH\""

mkdir -p "$CACHE_PATH"
mkdir -p "$CACHE_PATH/CompiledTemplates"
mkdir -p "$CACHE_PATH/SmartyCache"
chown -R $WEB_USER:$WEB_GROUP "$CACHE_PATH"
chmod -R 755 "$CACHE_PATH"

echo
echo "****************************"
echo "* eGloo Documentation Path *"
echo "****************************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_DOCUMENTATION\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own documentation path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own documentation path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e DOCUMENTATION_PATH
			echo
			echo "Location: \"$DOCUMENTATION_PATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done		
	;;

	# User chose the default path
	* ) DOCUMENTATION_PATH=$DEFAULT_DOCUMENTATION
	;;
esac

echo "Building documentation path..."
echo "\"$DOCUMENTATION_PATH\""

mkdir -p "$DOCUMENTATION_PATH"

echo
echo "**********************"
echo "* eGloo Logging Path *"
echo "**********************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_LOGPATH\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own log path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own log path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e LOGPATH
			echo
			echo "Location: \"$LOGPATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done		
	;;

	# User chose the default path
	* ) LOGPATH=$DEFAULT_LOGPATH
	;;
esac

echo "Building log path..."
echo "\"$LOGPATH\""

mkdir -p "$LOGPATH"
chown $WEB_USER:$WEB_GROUP "$LOGPATH"
chmod 755 "$LOGPATH"

echo
echo "*****************************"
echo "* eGloo Framework Root Path *"
echo "*****************************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_FRAMEWORKROOT\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own documentation path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own documentation path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e FRAMEWORK_PATH
			echo
			echo "Location: \"$FRAMEWORK_PATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done		
	;;

	# User chose the default path
	* ) FRAMEWORK_PATH=$DEFAULT_FRAMEWORKROOT
	;;
esac

echo "Building framework path..."
echo "\"$FRAMEWORK_PATH\""

if [ $USE_SYMLINKS ]
then
	if [ ! -e "$FRAMEWORK_PATH/PHP" ] && [  ! -L "$FRAMEWORK_PATH/PHP" ]
	then
		mkdir -p "$FRAMEWORK_PATH"
		ln -s "$PARENT_DIRECTORY/PHP" "$FRAMEWORK_PATH/PHP"
	else
		echo "Symlink exists"
	fi

	if [ ! -e "$FRAMEWORK_PATH/Templates" ] && [  ! -L "$FRAMEWORK_PATH/Templates" ]
	then
		mkdir -p "$FRAMEWORK_PATH"
		ln -s "$PARENT_DIRECTORY/Templates" "$FRAMEWORK_PATH/Templates"
	else
		echo "Symlink exists"
	fi

else
	mkdir -p "$FRAMEWORK_PATH"
	cp -R ../PHP "$FRAMEWORK_PATH/PHP"
fi

echo
echo "****************************"
echo "* Web Server Document Root *"
echo "****************************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_DOCUMENTROOT\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own documentation path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own documentation path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e DOCUMENT_PATH
			echo
			echo "Location: \"$DOCUMENT_PATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done		
	;;

	# User chose the default path
	* ) DOCUMENT_PATH=$DEFAULT_DOCUMENTROOT
	;;
esac

echo "Building web server document root path..."
echo "\"$DOCUMENT_PATH\""

mkdir -p "$DOCUMENT_PATH"

if [ $USE_SYMLINKS ]
then
	if [ ! -e "$DOCUMENT_PATH/.htaccess" ] && [  ! -L "$DOCUMENT_PATH/.htaccess" ]
	then
		mkdir -p "$DOCUMENT_PATH/"
		ln -s "$PARENT_DIRECTORY/DocRoot/.htaccess" "$DOCUMENT_PATH/.htaccess"
	else
		echo ".htaccess Symlink exists"
	fi

	if [ ! -e "$DOCUMENT_PATH/index.php" ] && [  ! -L "$DOCUMENT_PATH/index.php" ]
	then
		mkdir -p "$DOCUMENT_PATH/"
		ln -s "$PARENT_DIRECTORY/DocRoot/index.php" "$DOCUMENT_PATH/index.php"
	else
		echo "index.php Symlink exists"
	fi

	if [ ! -e "$DOCUMENT_PATH/PHP" ] && [  ! -L "$DOCUMENT_PATH/PHP" ]
	then
		mkdir -p "$DOCUMENT_PATH"
		ln -s "$PARENT_DIRECTORY/PHP" "$DOCUMENT_PATH/PHP"
		# Only do this next bit on Ubuntu... getcwd() is broken
		ln -s "$PARENT_DIRECTORY/PHP" "$PARENT_DIRECTORY/DocRoot/PHP"
	else
		echo "PHP Symlink exists"
	fi

	if [ ! -e "$DOCUMENT_PATH/Templates" ] && [  ! -L "$DOCUMENT_PATH/Templates" ]
	then
		mkdir -p "$DOCUMENT_PATH"
		ln -s "$PARENT_DIRECTORY/Templates" "$DOCUMENT_PATH/Templates"
		# Only do this next bit on Ubuntu... getcwd() is broken
		ln -s "$PARENT_DIRECTORY/Templates" "$PARENT_DIRECTORY/DocRoot/Templates"
	else
		echo "Templates Symlink exists"
	fi

	if [ ! -e "$DOCUMENT_PATH/XML" ] && [  ! -L "$DOCUMENT_PATH/XML" ]
	then
		mkdir -p "$DOCUMENT_PATH"
		ln -s "$PARENT_DIRECTORY/XML" "$DOCUMENT_PATH/XML"
		# Only do this next bit on Ubuntu... getcwd() is broken
		ln -s "$PARENT_DIRECTORY/XML" "$PARENT_DIRECTORY/DocRoot/XML"
	else
		echo "XML Symlink exists"
	fi

else
	cp "$PARENT_DIRECTORY/DocRoot/.htaccess" "$DOCUMENT_PATH/.htaccess"
	cp "$PARENT_DIRECTORY/DocRoot/index.php" "$DOCUMENT_PATH/.index.php"

	if [ ! -e "$DOCUMENT_PATH/PHP" ] && [  ! -L "$DOCUMENT_PATH/PHP" ]
	then
		mkdir -p "$DOCUMENT_PATH"
		ln -s "$FRAMEWORK_PATH/PHP" "$DOCUMENT_PATH/PHP"
	else
		echo "PHP Symlink exists"
	fi

fi

chown -R $WEB_USER:$WEB_GROUP "$DOCUMENT_PATH"
chmod -R 755 "$DOCUMENT_PATH"

echo
echo "*******************************"
echo "* eGloo Web Applications Path *"
echo "*******************************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_APPLICATIONS\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own documentation path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own documentation path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e APPLICATIONS_PATH
			echo
			echo "Location: \"$APPLICATIONS_PATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done		
	;;

	# User chose the default path
	* ) APPLICATIONS_PATH=$DEFAULT_APPLICATIONS
	;;
esac

echo "Building web applications path..."
echo "\"$APPLICATIONS_PATH\""

if [ $USE_SYMLINKS ]
then
	if [ ! -e "$APPLICATIONS_PATH" ]
	then
		mkdir -p "$APPLICATIONS_PATH"
		for filename in "$PARENT_DIRECTORY"/Applications/*
		do
		  ln -s "$PARENT_DIRECTORY/Applications/${filename##*/}" "$APPLICATIONS_PATH/${filename##*/}"
		done;
	else
		echo "Applications path exists"
		for filename in "$PARENT_DIRECTORY"/Applications/*
		do
			if [ ! -e "$APPLICATIONS_PATH/${filename##*/}" ] && [  ! -L "$APPLICATIONS_PATH/${filename##*/}" ]
			then
				ln -s "$PARENT_DIRECTORY/Applications/${filename##*/}" "$APPLICATIONS_PATH/${filename##*/}"
			else
				echo "Application ${filename##*/} Symlink exists"
			fi
		done;
	fi
else
	mkdir -p "$APPLICATIONS_PATH"
	cp -R "$PARENT_DIRECTORY/Applications/*" "$APPLICATIONS_PATH/"
fi

chown -R $WEB_USER:$WEB_GROUP "$APPLICATIONS_PATH"
chmod -R 755 "$APPLICATIONS_PATH"

echo
echo "********************"
echo "* eGloo Cubes Path *"
echo "********************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_CUBES\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own documentation path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own documentation path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e CUBES_PATH
			echo
			echo "Location: \"$CUBES_PATH\""
			echo
		
			echo -n "Use this location? [y/N]: "
			read -e CONFIRM_CONTINUE
		
			# Make sure user entered right path
			case "$CONFIRM_CONTINUE" in
				# New path is good, break the loop
				"Y" | "y" | "Yes" | "YES" | "yes" )
					NEW_PATH_SET=1
				;;
				# New path is no good, loop back
				* )
				;;
			esac
		done		
	;;

	# User chose the default path
	* ) CUBES_PATH=$DEFAULT_CUBES
	;;
esac

echo "Building cubes path..."
echo "\"$CUBES_PATH\""

if [ $USE_SYMLINKS ]
then
	if [ ! -e "$CUBES_PATH" ]
	then
		mkdir -p "$CUBES_PATH"
		for filename in "$PARENT_DIRECTORY"/Cubes/*
		do
		  ln -s "$PARENT_DIRECTORY/Cubes/${filename##*/}" "$CUBES_PATH/${filename##*/}"
		done;
	else
		echo "Cubes path exists"
		for filename in "$PARENT_DIRECTORY"/Cubes/*
		do
			if [ ! -e "$CUBES_PATH/${filename##*/}" ] && [  ! -L "$CUBES_PATH/${filename##*/}" ]
			then
				ln -s "$PARENT_DIRECTORY/Cubes/${filename##*/}" "$CUBES_PATH/${filename##*/}"
			else
				echo "Cube ${filename##*/} Symlink exists"
			fi
		done;
	fi
else
	mkdir -p "$CUBES_PATH"
	cp -R "$PARENT_DIRECTORY/Cubes/*" "$CUBES_PATH/"
fi

chown -R $WEB_USER:$WEB_GROUP "$CUBES_PATH"
chmod -R 755 "$CUBES_PATH"

# if [ $USE_SYMLINKS ]
# then
# 	mkdir -p "$CUBES_PATH"
# 	ln -s ../Cubes/B "$CUBES_PATH/B"
# 	ln -s ../Cubes/F "$CUBES_PATH/F"
# 	ln -s ../Cubes/P "$CUBES_PATH/P"
# else
# 	mkdir -p "$CUBES_PATH"
# 	cp -R ../Cubes/* "$CUBES_PATH"
# fi

# echo
# echo "********************"
# echo "* eGloo Cubes Root *"
# echo "********************"
# echo
# echo -n "Default Location: "
# echo "\"$DEFAULT_CUBES\""
# echo
# 
# echo -n "Use this location? [Y/n]: "
# read -e CONFIRM_CONTINUE
# 
# # Check if the user wants to use the default or specify their own documentation path
# case "$CONFIRM_CONTINUE" in
# 	# User chose to specify own documentation path
# 	"N" | "n" | "No" | "NO" | "no" )
# 		NEW_PATH_SET=0
# 		
# 		# Loop until we have a new path and the user has confirmed the location
# 		while [ "$NEW_PATH_SET" -ne 1 ]
# 		do
# 			echo -n "Enter New Location: "
# 			read -e CUBES_PATH
# 			echo
# 			echo "Location: \"$CUBES_PATH\""
# 			echo
# 		
# 			echo -n "Use this location? [y/N]: "
# 			read -e CONFIRM_CONTINUE
# 		
# 			# Make sure user entered right path
# 			case "$CONFIRM_CONTINUE" in
# 				# New path is good, break the loop
# 				"Y" | "y" | "Yes" | "YES" | "yes" )
# 					NEW_PATH_SET=1
# 				;;
# 				# New path is no good, loop back
# 				* )
# 				;;
# 			esac
# 		done		
# 	;;
# 
# 	# User chose the default path
# 	* ) CUBES_PATH=$DEFAULT_CUBES
# 	;;
# esac
# 
# echo "Building framework path..."
# echo "\"$CUBES_PATH\""
# 
# mkdir -p "$CUBES_PATH"

echo
echo "***********************"
echo "* Supporting Software *"
echo "***********************"
echo
echo "eGloo relies on several supporting software packages to be installed to run."
echo "For each supporting software package, the installer can offer the option to"
echo "install the required package from the eGloo distribution or prompt for a path"
echo "where the relevant software package can be found.  The installer will attempt"
echo "to make smart guesses about locations of software already installed."
echo

echo
echo "**************************"
echo "* Smarty Template Engine *"
echo "**************************"
echo

if [ -f "$DEFAULT_SMARTY" ]
then
	echo "Smarty was found at the following location."
	echo "\"$DEFAULT_SMARTY\""
	echo 
	echo -n "Use this Smarty package? [Y/n]: "
	read -e CONFIRM_CONTINUE

	# Check if the user wants to use the default or specify their own Smarty path
	case "$CONFIRM_CONTINUE" in
		# User chose to specify own Smarty path
		"N" | "n" | "No" | "NO" | "no" )
			NEW_PATH_SET=0

			# Loop until we have a new path and the user has confirmed the location
			while [ "$NEW_PATH_SET" -ne 1 ]
			do
				echo -n "Enter New Location: "
				read -e SMARTY_PATH
				echo
				echo "Location: \"$SMARTY_PATH\""
				echo

				echo -n "Use this location? [y/N]: "
				read -e CONFIRM_CONTINUE

				# Make sure user entered right path
				case "$CONFIRM_CONTINUE" in
					# New path is good, break the loop
					"Y" | "y" | "Yes" | "YES" | "yes" )
						NEW_PATH_SET=1
					;;
					# New path is no good, loop back
					* )
					;;
				esac
			done		
		;;

		# User chose the default path
		* ) SMARTY_PATH=$DEFAULT_SMARTY
		;;
	esac
else
	echo "Smarty was not found at the default location."
	echo 
	echo -n "Install Smarty? (If no, you will be prompted for an existing Smarty install) [Y/n]: "
	read -e CONFIRM_CONTINUE
	
	# Check if the user wants to use the default or specify their own Smarty path
	case "$CONFIRM_CONTINUE" in
		# User chose to specify own Smarty path
		"N" | "n" | "No" | "NO" | "no" )
			NEW_PATH_SET=0

			# Loop until we have a new path and the user has confirmed the location
			while [ "$NEW_PATH_SET" -ne 1 ]
			do
				echo -n "Enter New Location: "
				read -e SMARTY_PATH
				echo
				echo "Location: \"$SMARTY_PATH\""
				echo

				echo -n "Use this location? [y/N]: "
				read -e CONFIRM_CONTINUE

				# Make sure user entered right path
				case "$CONFIRM_CONTINUE" in
					# New path is good, break the loop
					"Y" | "y" | "Yes" | "YES" | "yes" )
						NEW_PATH_SET=1
					;;
					# New path is no good, loop back
					* )
					;;
				esac
			done		
		;;

		# User chose the default path
		* )
			# TODO install Smarty
			SMARTY_PATH=$DEFAULT_SMARTY
		;;
	esac

fi

chown -R $WEB_USER:$WEB_GROUP "$SMARTY_PATH"
chmod -R 755 "$SMARTY_PATH"

echo
echo "***********************"
echo "* Doctrine ORM Engine *"
echo "***********************"
echo

if [ -f "$DEFAULT_DOCTRINE" ]
then
	echo "Doctrine was found at the following location."
	echo "\"$DEFAULT_DOCTRINE\""
	echo 
	echo -n "Use this Doctrine package? [Y/n]: "
	read -e CONFIRM_CONTINUE

	# Check if the user wants to use the default or specify their own Doctrine path
	case "$CONFIRM_CONTINUE" in
		# User chose to specify own Doctrine path
		"N" | "n" | "No" | "NO" | "no" )
			NEW_PATH_SET=0

			# Loop until we have a new path and the user has confirmed the location
			while [ "$NEW_PATH_SET" -ne 1 ]
			do
				echo -n "Enter New Location: "
				read -e DOCTRINE_PATH
				echo
				echo "Location: \"$DOCTRINE_PATH\""
				echo

				echo -n "Use this location? [y/N]: "
				read -e CONFIRM_CONTINUE

				# Make sure user entered right path
				case "$CONFIRM_CONTINUE" in
					# New path is good, break the loop
					"Y" | "y" | "Yes" | "YES" | "yes" )
						NEW_PATH_SET=1
					;;
					# New path is no good, loop back
					* )
					;;
				esac
			done		
		;;

		# User chose the default path
		* ) DOCTRINE_PATH=$DEFAULT_DOCTRINE
		;;
	esac
else
	echo "Doctrine was not found at the default location."
	echo 
	echo -n "Install Doctrine? (If no, you will be prompted for an existing Doctrine install) [Y/n]: "
	read -e CONFIRM_CONTINUE
	
	# Check if the user wants to use the default or specify their own Doctrine path
	case "$CONFIRM_CONTINUE" in
		# User chose to specify own Doctrine path
		"N" | "n" | "No" | "NO" | "no" )
			NEW_PATH_SET=0

			# Loop until we have a new path and the user has confirmed the location
			while [ "$NEW_PATH_SET" -ne 1 ]
			do
				echo -n "Enter New Location: "
				read -e DOCTRINE_PATH
				echo
				echo "Location: \"$DOCTRINE_PATH\""
				echo

				echo -n "Use this location? [y/N]: "
				read -e CONFIRM_CONTINUE

				# Make sure user entered right path
				case "$CONFIRM_CONTINUE" in
					# New path is good, break the loop
					"Y" | "y" | "Yes" | "YES" | "yes" )
						NEW_PATH_SET=1
					;;
					# New path is no good, loop back
					* )
					;;
				esac
			done		
		;;

		# User chose the default path
		* )
			# TODO install Doctrine
			DOCTRINE_PATH=$DEFAULT_DOCTRINE
		;;
	esac

fi

chown -R $WEB_USER:$WEB_GROUP "$DOCTRINE_PATH"
chmod -R 755 "$DOCTRINE_PATH"

echo 
echo "Writing configuration files... "

if [ $DETECTED_PLATFORM -eq $OS_WINDOWS_XP_CYGWIN ]
then
	./Configure.php \
		--ApplicationsPath="c:/cygwin$APPLICATIONS_PATH" \
		--CachePath="c:/cygwin$CACHE_PATH" \
		--CompiledTemplatesPath=blah \
		--ConfigurationPath="c:/cygwin$CONFIG_PATH" \
		--CubesPath="c:/cygwin$CUBES_PATH" \
		--DoctrinePath="c:/cygwin$DOCTRINE_PATH" \
		--DocumentationPath="c:/cygwin$DOCUMENTATION_PATH" \
		--DocumentRoot="c:/cygwin$DOCUMENT_PATH" \
		--FrameworkRootPath="c:/cygwin$FRAMEWORK_PATH" \
		--LoggingPath="c:/cygwin$LOGPATH" \
		--SmartyPath="c:/cygwin$SMARTY_PATH" \
		--UseDoctrine="true" \
		--UseSmarty="true" \
		--WriteLocalizationPaths="true"
else
	./Configure.php \
		--ApplicationsPath="$APPLICATIONS_PATH" \
		--CachePath="$CACHE_PATH" \
		--CompiledTemplatesPath=blah \
		--ConfigurationPath="$CONFIG_PATH" \
		--CubesPath="$CUBES_PATH" \
		--DoctrinePath="$DOCTRINE_PATH" \
		--DocumentationPath="/$DOCUMENTATION_PATH" \
		--DocumentRoot="$DOCUMENT_PATH" \
		--FrameworkRootPath="$FRAMEWORK_PATH" \
		--LoggingPath="$LOGPATH" \
		--SmartyPath="$SMARTY_PATH" \
		--UseDoctrine="true" \
		--UseSmarty="true" \
		--WriteLocalizationPaths="true"
fi

# Configure script will make ownership of the child cache directories root so switch it back
chown -R $WEB_USER:$WEB_GROUP $CACHE_PATH

if [ $USE_SYMLINKS ]
then
	mv "ConfigCache.php" "$PARENT_DIRECTORY/DocRoot/ConfigCache.php"

	if [ ! -e "$DOCUMENT_PATH/ConfigCache.php" ] && [  ! -L "$DOCUMENT_PATH/ConfigCache.php" ]
	then
		ln -s "$PARENT_DIRECTORY/DocRoot/ConfigCache.php" "$DOCUMENT_PATH/ConfigCache.php"
	else
		echo "ConfigCache Symlink exists"
	fi
else
	mv "ConfigCache.php" "$DOCUMENT_PATH/ConfigCache.php"
fi


# Set ownership on the config dump created
chown -R $WEB_USER:$WEB_GROUP "$DOCUMENT_PATH/ConfigCache.php"
chmod -R 755 "$DOCUMENT_PATH"

echo
echo "Done"

exit