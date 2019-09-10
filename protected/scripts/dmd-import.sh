#!/bin/bash -l

# /**
#  * OpenEyes
#  *
#  * (C) OpenEyes Foundation, 2019
#  * This file is part of OpenEyes.
#  * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
#  * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
#  * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
#  *
#  * @package OpenEyes
#  * @link http://www.openeyes.org.uk
#  * @author OpenEyes <info@openeyes.org.uk>
#  * @copyright Copyright (c) 2019, OpenEyes Foundation
#  * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
#  */

## NOTE: This script assumes it is in protected/scripts. If you move it then relative paths will not work!

# Find fuill folder path where this script is located, then find root folder
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
SCRIPTDIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
WROOT="$( cd -P "$SCRIPTDIR/../../" && pwd )"
MODULEROOT=$WROOT/protected/modules

# Process commandline parameters

setsimportfile="$WROOT/protected/data/dmd_data/Drug-Type-Mappings.xlsx"
nofix=0
showhelp=0

PARAMS=()
while [[ $# -gt 0 ]]
do
    p="$1"

    case $p in
    	--setsimportfile)
            setsimportfile="$2"
            shift
    	;;
    	--no-fix) nofix=1
    		## do not run oe-fix (useful when calling from other scripts)
    	;;
            --help|/?|?) showhelp=1
            ## Show the help text
        ;;
    	*)  # Hold for processing later
            PARAMS+=("$p")
		;;
    esac
    shift # move to next parameter
done

# # Pass any additional parameters to other commands
# if  [ ${#PARAMS[@]} -gt 0 ]; then
#     if [ "$branch" != "0" ]; then
# 		checkoutparams="$chekoutparams --depth 1 --single-branch"
#         for i in "${PARAMS[@]}"
#         do
#             checkoutparams="$checkoutparams $i"
#         done
#     else
#         echo "Unknown Parameter(s):"
#         for i in "${PARAMS[@]}"
#         do
#             echo $i
#         done
#         exit 1;
#     fi
# fi

if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Imports a new DM+D dataset into openeyes"
    echo ""
    echo "usage: $0 [-setsfilename <path> ]"
    echo ""
    echo "COMMAND OPTIONS:"
    echo "	--help         : Display this help text"
    echo "	--setsimportfile"
	echo "	   <path to file> : import sets from a given xlsx file"
	echo ""
    exit 1
fi

echo -e "\nRunning DM+D import. Using sets spec from $setsimportfile ....\n"
php $WROOT/protected/yiic importdrugs import
php $WROOT/protected/yiic importdrugs copytooe
php $WROOT/protected/yiic medicationsetimport --filename=/var/www/openeyes/protected/data/dmd_data/Drug-Type-Mappings.xlsx
php $WROOT/protected/yiic populateautomedicationsets
php $WROOT/protected/yiic localmedicationtodmdmedication
