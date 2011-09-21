#! /usr/bin/env bash
#
# eGloo Framework Installation Script (OS X)
#
# Script should be chmod 700 and run as root from the working directory
# using the command ./Install.sh
#
# Copyright 2011 eGloo, LLC
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
# @copyright 2011 eGloo, LLC
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

# Current platform
# Default to Debian
DETECTED_PLATFORM=0

# Get our parent directory
PARENT_DIRECTORY=$(_egloo_parent_dir=$(pwd) ; echo "${_egloo_parent_dir%/*}")

# Get our platform
PLATFORM=$(./shtool platform -v -F "%sc (%ac) %st (%at) %sp (%ap)")

# Get our Timestamp
TIMESTAMP=$(date +%s)

checkUserCanRead() {
	USER_CAN_READ=`su -m $1 -c "./ckwuread.sh '$2'"`

	if [ ! $USER_CAN_READ -eq 1 ]
	then
		echo
		echo "WARNING: User '$1' cannot read $2"
		echo "Please fix permissions for this path after this install has completed."
		echo
	fi

   return 0
}

# Temporarily disable errexit check because grep returns non-true on a result we need
set +o errexit
MACOSX_FOUND=`echo "$PLATFORM" | grep -i -c "Apple Mac OS X"`
UBUNTU_FOUND=`echo "$PLATFORM" | grep -i -c "Ubuntu"`
WINDOWS_FOUND=`echo "$PLATFORM" | grep -i -c "Windows"`
set -o errexit

if [ "$MACOSX_FOUND" -eq 1 ]
then
	echo "Detected Apple Mac OS X"
	DETECTED_PLATFORM=$OS_MACOSX

	if [ ! -f "/var/db/locate.database" ]
	then
		echo "**************************************************************************"
		echo "* WARNING: The locate database (/var/db/locate.database) does not exist. *"
		echo "**************************************************************************"
		echo
		echo "This installer uses locate to determine the location of supporting software during the installation process."
		echo "You can proceed and enter supporting software paths manually, but it is recommended that you activate the locate Launch Daemon instead:"
		echo
		echo "sudo launchctl load -w /System/Library/LaunchDaemons/com.apple.locate.plist"
		echo

		echo -n "Continue? [y/N]: "
		read -e CONFIRM_CONTINUE

		# Make sure the user is prepared to answer some setup questions
		case "$CONFIRM_CONTINUE" in
			"Y" | "y" | "Yes" | "YES" | "yes" )
				echo
				echo "Continuing without locate database.  Safety not guaranteed."
				echo
			;;

			* )
				echo "Installation Aborted"
				exit
			;;
		esac
	fi

	# Default Configuration Parameters (OS X)
	DEFAULT_APPLICATIONS="/Library/Application Support/eGloo/Applications"
	DEFAULT_CACHE_DIR="/Library/Caches/com.egloo.templates"
	DEFAULT_CONFIG="/Library/Application Support/eGloo/Framework/Configuration"
	DEFAULT_CUBES="/Library/Application Support/eGloo/Cubes"
	DEFAULT_DATA_STORE="/Library/Application Support/eGloo/Data"
	DEFAULT_DOCTRINE=`locate /opt/*lib/Doctrine.php | head -n 1`
	DEFAULT_DOCUMENTATION="/Library/Documentation/eGlooFramework"
	DEFAULT_DOCUMENTROOT="/Library/WebServer/eGloo"
	DEFAULT_FRAMEWORKROOT="/Library/Frameworks/eGloo.framework"
	DEFAULT_LOGPATH="/Library/Logs/eGloo"
	DEFAULT_SMARTY=`locate /opt/*libs/Smarty.class.php | head -n 1`
	DEFAULT_WEBUSER="_www"
	DEFAULT_WEBGROUP="admin"
fi

if [ "$UBUNTU_FOUND" -eq 1 ]
then
	echo "Detected Ubuntu Linux"
	DETECTED_PLATFORM=$OS_UBUNTU

	# Default Configuration Parameters (Ubuntu)
	DEFAULT_APPLICATIONS="/usr/lib/egloo/applications"
	DEFAULT_CACHE_DIR="/var/cache/egloo"
	DEFAULT_CONFIG="/etc/egloo/"
	DEFAULT_CUBES="/usr/lib/egloo/cubes"
	DEFAULT_DATA_STORE="/var/egloo"
	DEFAULT_DOCTRINE=`locate /usr/*/Doctrine.php | head -n 1`
	DEFAULT_DOCUMENTATION="/usr/share/doc/egloo"
	DEFAULT_DOCUMENTROOT="/var/www/egloo"
	DEFAULT_FRAMEWORKROOT="/usr/lib/eglooframework"
	DEFAULT_LOGPATH="/var/log/egloo"
	DEFAULT_SMARTY=`locate /usr/*/Smarty.class.php | head -n 1`

	DEFAULT_WEBUSER="www-data"
	DEFAULT_WEBGROUP="www-data"
fi

if [ "$WINDOWS_FOUND" -eq 1 ]
then
	echo "Detected Windows XP (Cygwin)"
	DETECTED_PLATFORM=$OS_WINDOWS

	# Default Configuration Parameters (Windows XP with Cygwin)
	DEFAULT_APPLICATIONS="/lib/egloo/applications"
	DEFAULT_CACHE_DIR="/var/cache/egloo"
	DEFAULT_CONFIG="/etc/egloo/"
	DEFAULT_CUBES="/lib/egloo/cubes"
	DEFAULT_DATA_STORE="/var/egloo"
	DEFAULT_DOCTRINE="/usr/share/php/doctrine/Doctrine.php"
	DEFAULT_DOCUMENTATION="/usr/share/doc/egloo"
	DEFAULT_DOCUMENTROOT="/cygdrive/c/wamp/www/egloo"
	DEFAULT_FRAMEWORKROOT="/lib/eglooframework"
	DEFAULT_LOGPATH="/var/log/egloo"
	DEFAULT_SMARTY="/usr/share/php/smarty/Smarty.class.php"
	DEFAULT_WEBUSER="user"
	DEFAULT_WEBGROUP="mkpasswd"
fi

# Check for root
if [[ "$UID" -ne "$ROOT_UID" && $DETECTED_PLATFORM -ne $OS_WINDOWS ]]
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

# If we're not on Windows, allow a symlink based install
if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
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
else
	# There's a bug with Windows Symlinks, PHP and Apache, so let's just not allow this option
	USE_SYMLINKS=false
fi

# If we're on Windows let's use hard links
if [ $DETECTED_PLATFORM -eq $OS_WINDOWS ]
then
	LINKCMD="ln -s"
else
	LINKCMD="ln -s"
fi

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

printf "Building configuration files... "

if [ "$USE_SYMLINKS" = "true" ]
then
	mkdir -p "$CONFIG_PATH"
else
	mkdir -p "$CONFIG_PATH"
	cp -R "../Configuration/Smarty" "$CONFIG_PATH"
fi

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown $WEB_USER:$WEB_GROUP "$CONFIG_PATH"
	chmod 755 "$CONFIG_PATH"

	checkUserCanRead "$WEB_USER" "$CONFIG_PATH"
else
	echo "Ignoring ownership and permissions of Configuration Path for Windows"
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

printf "Building cache path... "

mkdir -p "$CACHE_PATH"

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown -R $WEB_USER:$WEB_GROUP "$CACHE_PATH"
	chmod -R 755 "$CACHE_PATH"

	checkUserCanRead "$WEB_USER" "$CACHE_PATH"
else
	echo "Ignoring ownership and permissions of Cache Path for Windows"
fi

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

printf "Building documentation path... "

mkdir -p "$DOCUMENTATION_PATH"

printf "done.\n"

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

printf "Building log path... "

mkdir -p "$LOGPATH"

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown $WEB_USER:$WEB_GROUP "$LOGPATH"
	chmod 755 "$LOGPATH"

	checkUserCanRead "$WEB_USER" "$LOGPATH"
else
	chmod 777 "$LOGPATH"
fi

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

printf "Building framework path... "

mkdir -p "$FRAMEWORK_PATH"

if [ "$USE_SYMLINKS" = "true" ]
then
	if [ ! -e "$FRAMEWORK_PATH/Images" ] && [  ! -L "$FRAMEWORK_PATH/Images" ]
	then
		# mkdir -p "$FRAMEWORK_PATH"
	
		# Even if we're using Windows, NTFS does not allow hardlinks to directories
		ln -s "$PARENT_DIRECTORY/Images" "$FRAMEWORK_PATH/Images"
	else
		echo "Framework images symlink exists"
	fi

	if [ ! -e "$FRAMEWORK_PATH/Library" ] && [  ! -L "$FRAMEWORK_PATH/Library" ]
	then
		# mkdir -p "$FRAMEWORK_PATH"
	
		# Even if we're using Windows, NTFS does not allow hardlinks to directories
		ln -s "$PARENT_DIRECTORY/Library" "$FRAMEWORK_PATH/Library"
	else
		echo "Framework 3rd party library symlink exists"
	fi

	if [ ! -e "$FRAMEWORK_PATH/PHP" ] && [  ! -L "$FRAMEWORK_PATH/PHP" ]
	then
		# mkdir -p "$FRAMEWORK_PATH"
		
		# Even if we're using Windows, NTFS does not allow hardlinks to directories
		ln -s "$PARENT_DIRECTORY/PHP" "$FRAMEWORK_PATH/PHP"
	else
		echo "Framework PHP symlink exists"
	fi

	if [ ! -e "$FRAMEWORK_PATH/Templates" ] && [  ! -L "$FRAMEWORK_PATH/Templates" ]
	then
		# mkdir -p "$FRAMEWORK_PATH"

		# Even if we're using Windows, NTFS does not allow hardlinks to directories
		ln -s "$PARENT_DIRECTORY/Templates" "$FRAMEWORK_PATH/Templates"
	else
		echo "Framework templates symlink exists"
	fi

	if [ ! -e "$FRAMEWORK_PATH/XML" ] && [  ! -L "$FRAMEWORK_PATH/XML" ]
	then
		# mkdir -p "$DOCUMENT_ROOT"
	
		# Even if we're using Windows, NTFS does not allow hardlinks to directories
		ln -s "$PARENT_DIRECTORY/XML" "$FRAMEWORK_PATH/XML"
		# Only do this next bit on Ubuntu... getcwd() is broken
		# ln -s "$PARENT_DIRECTORY/XML" "$PARENT_DIRECTORY/DocRoot/XML"
	else
		echo "Framework XML symlink exists"
	fi

else
	mkdir -p "$FRAMEWORK_PATH"

	cp -R "$PARENT_DIRECTORY/Images" "$FRAMEWORK_PATH/"
	cp -R "$PARENT_DIRECTORY/PHP" "$FRAMEWORK_PATH/"
	cp -R "$PARENT_DIRECTORY/Templates" "$FRAMEWORK_PATH/"
	cp -R "$PARENT_DIRECTORY/XML" "$FRAMEWORK_PATH/"

fi

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
#	chown -R root:wheel "$FRAMEWORK_PATH"
	chmod -R 755 "$FRAMEWORK_PATH"

	checkUserCanRead "$WEB_USER" "$FRAMEWORK_PATH"
else
	echo "Ignoring ownership and permissions of Log Path for Windows"
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
			read -e DOCUMENT_ROOT
			echo
			echo "Location: \"$DOCUMENT_ROOT\""
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
	* ) DOCUMENT_ROOT=$DEFAULT_DOCUMENTROOT
	;;
esac

printf "Building web server document root path... "

mkdir -p "$DOCUMENT_ROOT"

if [ "$USE_SYMLINKS" = "true" ]
then
	if [ ! -e "$DOCUMENT_ROOT/index.php" ] && [  ! -L "$DOCUMENT_ROOT/index.php" ]
	then
		# mkdir -p "$DOCUMENT_ROOT"
		$LINKCMD "$PARENT_DIRECTORY/DocRoot/index.php" "$DOCUMENT_ROOT/index.php"
	else
		echo "index.php Symlink exists"
	fi

	if [ ! -e "$DOCUMENT_ROOT/PHP" ] && [  ! -L "$DOCUMENT_ROOT/PHP" ]
	then
		# Even if we're using Windows, NTFS does not allow hardlinks to directories
		ln -s "$PARENT_DIRECTORY/PHP" "$DOCUMENT_ROOT/PHP"
	else
		echo "PHP Symlink exists"
	fi

	if [ ! -e "$PARENT_DIRECTORY/DocRoot/PHP" ] && [  ! -L "$PARENT_DIRECTORY/DocRoot/PHP" ] && [ $DETECTED_PLATFORM -eq $OS_UBUNTU ]
	then
		echo "Creating PHP symlink shim for Ubuntu because getcwd() is broken"
		# Only do this next bit on Ubuntu... getcwd() is broken
		ln -s "$PARENT_DIRECTORY/PHP" "$PARENT_DIRECTORY/DocRoot/PHP"
	else
		echo "PHP Symlink shim for getcwd() exists"
	fi
else
	cp "$PARENT_DIRECTORY/DocRoot/index.php" "$DOCUMENT_ROOT/index.php"

	mkdir -p "$DOCUMENT_ROOT/PHP/Includes"

	cp "$PARENT_DIRECTORY/PHP/Includes/eGlooAutoload.php" "$DOCUMENT_ROOT/PHP/Includes/eGlooAutoload.php"

	mkdir -p "$DOCUMENT_ROOT/PHP/Classes/Utilities"

	cp "$PARENT_DIRECTORY/PHP/Classes/Utilities/eGlooConfiguration.php" "$DOCUMENT_ROOT/PHP/Classes/Utilities/eGlooConfiguration.php"
	cp "$PARENT_DIRECTORY/PHP/Classes/Utilities/eGlooLogger.php" "$DOCUMENT_ROOT/PHP/Classes/Utilities/eGlooLogger.php"
fi

if [ -e "$DOCUMENT_ROOT/.htaccess" ]
then
	cp "$DOCUMENT_ROOT/.htaccess" "$DOCUMENT_ROOT/.htaccess.$TIMESTAMP.old"
fi

cp "$PARENT_DIRECTORY/DocRoot/.htaccess" "$DOCUMENT_ROOT/.htaccess"

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown -R $WEB_USER:$WEB_GROUP "$DOCUMENT_ROOT"
	chmod -R 755 "$DOCUMENT_ROOT"

	checkUserCanRead "$WEB_USER" "$DOCUMENT_ROOT"
else
	echo "Ignoring ownership and permissions of Document Root for Windows"
fi

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

printf "Building web applications path... "

if [ "$USE_SYMLINKS" = "true" ]
then
	if [ ! -e "$APPLICATIONS_PATH" ]
	then
		mkdir -p "$APPLICATIONS_PATH"
		for filename in "$PARENT_DIRECTORY"/Applications/*
		do
			# Even if we're using Windows, NTFS does not allow hardlinks to directories
			ln -s "$PARENT_DIRECTORY/Applications/${filename##*/}" "$APPLICATIONS_PATH/${filename##*/}"
		done;
	else
		echo "Applications path exists"
		for filename in "$PARENT_DIRECTORY"/Applications/*
		do
			if [ ! -e "$APPLICATIONS_PATH/${filename##*/}" ] && [  ! -L "$APPLICATIONS_PATH/${filename##*/}" ]
			then
				# Even if we're using Windows, NTFS does not allow hardlinks to directories
				ln -s "$PARENT_DIRECTORY/Applications/${filename##*/}" "$APPLICATIONS_PATH/${filename##*/}"
			else
				echo "Application ${filename##*/} Symlink exists"
			fi
		done;
	fi
else
	mkdir -p "$APPLICATIONS_PATH"
	cp -R "$PARENT_DIRECTORY"/Applications/* "$APPLICATIONS_PATH/"
fi

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown -R $WEB_USER:$WEB_GROUP "$APPLICATIONS_PATH"
	chmod -R 755 "$APPLICATIONS_PATH"

	checkUserCanRead "$WEB_USER" "$APPLICATIONS_PATH"
else
	echo "Ignoring ownership and permissions of Applications Path for Windows"
fi

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

printf "Building cubes path... "

if [ "$USE_SYMLINKS" = "true" ]
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
	cp -R "$PARENT_DIRECTORY"/Cubes/* "$CUBES_PATH/"
fi

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown -R $WEB_USER:$WEB_GROUP "$CUBES_PATH"
	chmod -R 755 "$CUBES_PATH"

	checkUserCanRead "$WEB_USER" "$CUBES_PATH"
else
	echo "Ignoring ownership and permissions of Cubes Path for Windows"
fi

echo
echo "*************************"
echo "* eGloo Data Store Path *"
echo "*************************"
echo
echo -n "Default Location: "
echo "\"$DEFAULT_DATA_STORE\""
echo

echo -n "Use this location? [Y/n]: "
read -e CONFIRM_CONTINUE

# Check if the user wants to use the default or specify their own data store path
case "$CONFIRM_CONTINUE" in
	# User chose to specify own data store path
	"N" | "n" | "No" | "NO" | "no" )
		NEW_PATH_SET=0
		
		# Loop until we have a new path and the user has confirmed the location
		while [ "$NEW_PATH_SET" -ne 1 ]
		do
			echo -n "Enter New Location: "
			read -e DATA_STORE_PATH
			echo
			echo "Location: \"$DATA_STORE_PATH\""
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
	* ) DATA_STORE_PATH=$DEFAULT_DATA_STORE
	;;
esac

printf "Building data store path... "

mkdir -p "$DATA_STORE_PATH"

printf "done.\n"

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown -R $WEB_USER:$WEB_GROUP "$DATA_STORE_PATH"
	chmod -R 755 "$DATA_STORE_PATH"

	checkUserCanRead "$WEB_USER" "$DATA_STORE_PATH"
else
	echo "Ignoring ownership and permissions of Data Store Path for Windows"
fi

echo
echo "***********************"
echo "* Supporting Software *"
echo "***********************"
echo
echo "eGloo relies on several supporting software packages to be installed to run."
echo "For each supporting software package, the installer can offer the option to"
echo "use the required package provided with the eGloo distribution or prompt for a path"
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
	echo -n "Use Smarty package provided in eGloo Framework library? (If no, you will be prompted for an existing Smarty install) [Y/n]: "
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
			SMARTY_PATH="$FRAMEWORK_PATH/Library/Smarty3/libs/Smarty.class.php"
		;;
	esac

fi

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	checkUserCanRead "$WEB_USER" "$SMARTY_PATH"
else
	echo "Ignoring ownership and permissions of Smarty Path for Windows"
fi

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
	echo -n "Use Doctrine package provided in eGloo Framework library? (If no, you will be prompted for an existing Doctrine install) [Y/n]: "
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
			DOCTRINE_PATH="$FRAMEWORK_PATH/Library/Doctrine/lib/Doctrine.php"
		;;
	esac

fi

if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	checkUserCanRead "$WEB_USER" "$DOCTRINE_PATH"
else
	echo "Ignoring ownership and permissions of Doctrine Path for Windows"
fi

echo 
printf "Writing configuration files... "

if [ $DETECTED_PLATFORM -eq $OS_WINDOWS ]
then
	chmod -R 777 "$CACHE_PATH"
	./Configure.php \
		--ApplicationsPath="c:/cygwin$APPLICATIONS_PATH" \
		--CachePath="c:/cygwin$CACHE_PATH" \
		--ConfigurationPath="c:/cygwin$CONFIG_PATH" \
		--CubesPath="c:/cygwin$CUBES_PATH" \
		--DataStorePath="c:/cygwin$DATA_STORE_PATH" \
		--DoctrinePath="c:/cygwin$DOCTRINE_PATH" \
		--DocumentationPath="c:/cygwin$DOCUMENTATION_PATH" \
		--DocumentRoot="c:$DOCUMENT_ROOT" \
		--FrameworkRootPath="c:/cygwin$FRAMEWORK_PATH" \
		--LoggingPath="c:/cygwin$LOGPATH" \
		--SmartyPath="c:/cygwin$SMARTY_PATH" \
		--UseDoctrine="true" \
		--UseSmarty="true" \
		--WriteLocalizationPaths="true"
	chmod -R 755 "$CACHE_PATH"
else
	./Configure.php \
		--ApplicationsPath="$APPLICATIONS_PATH" \
		--CachePath="$CACHE_PATH" \
		--ConfigurationPath="$CONFIG_PATH" \
		--CubesPath="$CUBES_PATH" \
		--DataStorePath="$DATA_STORE_PATH" \
		--DoctrinePath="$DOCTRINE_PATH" \
		--DocumentationPath="$DOCUMENTATION_PATH" \
		--DocumentRoot="$DOCUMENT_ROOT" \
		--FrameworkRootPath="$FRAMEWORK_PATH" \
		--LoggingPath="$LOGPATH" \
		--SmartyPath="$SMARTY_PATH" \
		--UseDoctrine="false" \
		--UseSmarty="true" \
		--WriteLocalizationPaths="true"
fi

# Configure script will make ownership of the child cache directories root so switch it back
if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chown -R $WEB_USER:$WEB_GROUP "$CACHE_PATH"
	chmod -R 755 "$CACHE_PATH"
	
	checkUserCanRead "$WEB_USER" "$CACHE_PATH"
else
	chmod -R 777 "$CACHE_PATH"
#	echo "Ignoring ownership of Cache Path for Windows"
fi

printf "done.\n"

printf "Copying generated configuration to appropriate paths... "

if [ -e "$CONFIG_PATH/System.xml" ]
then
	mkdir -p "$CONFIG_PATH/InactiveConfigurations/$TIMESTAMP"
	cp "$CONFIG_PATH/System.xml" "$CONFIG_PATH/InactiveConfigurations/$TIMESTAMP/System.xml"
fi

cp "System.xml" "$CONFIG_PATH/System.xml"

if [ -e "$DOCUMENT_ROOT/System.xml" ]
then
	cp "$DOCUMENT_ROOT/System.xml" "$DOCUMENT_ROOT/System.xml.$TIMESTAMP.old"
fi

cp "System.xml" "$DOCUMENT_ROOT/System.xml"

cp "System.xml" "Config.xml"

if [ -e "$CONFIG_PATH/Config.xml" ]
then
	mkdir -p "$CONFIG_PATH/InactiveConfigurations/$TIMESTAMP"
	cp "$CONFIG_PATH/Config.xml" "$CONFIG_PATH/InactiveConfigurations/$TIMESTAMP/Config.xml"
fi

cp "Config.xml" "$CONFIG_PATH/Config.xml"

if [ -e "$DOCUMENT_ROOT/Config.xml" ]
then
	cp "$DOCUMENT_ROOT/Config.xml" "$DOCUMENT_ROOT/Config.xml.$TIMESTAMP.old"
fi

cp "Config.xml" "$DOCUMENT_ROOT/Config.xml"

rm "System.xml"
rm "Config.xml"

printf "done.\n"

# Set ownership on the config dump created
if [ $DETECTED_PLATFORM -ne $OS_WINDOWS ]
then
	chmod 664 "$CONFIG_PATH/Config.xml"
	chmod 644 "$CONFIG_PATH/System.xml"

	chmod -R 755 "$DOCUMENT_ROOT"

	chmod 664 "$DOCUMENT_ROOT/Config.xml"
	chown $WEB_USER:$WEB_GROUP "$DOCUMENT_ROOT/Config.xml"
	
	chmod 640 "$DOCUMENT_ROOT/System.xml"
	chown $WEB_USER:$WEB_GROUP "$DOCUMENT_ROOT/System.xml"
else
	echo "Ignoring permissions for Document Root for Windows"
fi

echo
echo "Installation Complete"

exit