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
# @copyright 2010 eGloo, LLC
# @license http://www.apache.org/licenses/LICENSE-2.0
# @package Build
# @subpackage Installation
# @version 1.0

# Exit the script if any variables specified are unset when accessed
# set -o nounset

# Exit the script if any statement returns a non-true value
# This prevents errors from cascading
# set -o errexit

# Exit Error Codes
E_XCD=01       # Script can't change directories
E_NOTROOT=02   # Script not run as root

# Script Constants
ROOT_UID=0     # Only users with $UID 0 have root privileges.

RETURN=0

if [ -r "$1" ]
then
	# echo "Can read $1"
	RETURN=1
else
	# echo "Can't read $1"
	RETURN=0
fi

echo $RETURN

exit 0