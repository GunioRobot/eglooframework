#! /bin/bash
#
# eGloo Framework Installation Script (OS X)
#
# Script should be chmod 700 and run as root from the working directory
# using the command ./Install.sh
#
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

# Default Configuration Parameters (OS X)
DEFAULT_CACHE_DIR="/Library/Caches/eGlooFramework"
DEFAULT_CONFIG="/Library/Application Support/eGlooFramework/Configuration"
DEFAULT_DOCROOT="/Library/Application Support/eGlooFramework/DocRoot"
DEFAULT_DOCUMENTATION="/Library/Documentation/eGlooFramework"
DEFAULT_LOGPATH="/Library/Logs/eGlooFramework"

# Check for root
if [ "$UID" -ne "$ROOT_UID" ]
then
	echo "***********************************"
	echo "* Must be root to run this script *"
	echo "***********************************"
	echo
	#exit $E_NOTROOT
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

mkdir -p "$CONFIG_PATH"

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

# Clean exit
exit


