#!/bin/bash

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

# set command options
force=0
cleanconfig=0
username=""
pass=""
httpuserstring=""
usessh=0
sshuserstring="git"
showhelp=0
checkoutparams="-f --no-migrate --no-summary --no-fix"
accept=0
genetics=0
preservedb=0

# Process command line inputs
while [[ $# -gt 0 ]]
do
    case $1 in
        --accept) accept=1;
        		## Accepts the disclaimer, without pausing the installation
        ;;
    	--root|-r|--r|--remote) checkoutparams="$checkoutparams $1"
        ;;
        --ssh|-ssh) usessh=1; checkoutparams="$checkoutparams $1"
    	;;
        --https) usessh=0; checkoutparams="$checkoutparams $1"
        ;;
        --genetics) genetics=1
        ;;
        --help) showhelp=1
        ;;
        --no-checkout|-nc) nocheckout=1
            # Don't perform checkout (needed if using pre-built code)
        ;;
        --preserve-database) preservedb=1
            # use an existing database (won't call oe-reset)
        ;;
    	*)  checkoutparams="$checkoutparams $1"
            # Pass anything else through to the checkout command
        ;;
    esac
    shift
done

# Show help text
if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Installs the openeyes application"
    echo ""
    echo "usage: $0 <branch> [--help] [--force | -f] [--no-migrate | -n] [--kill-modules | -ff ] [--no-compile] [-r <remote>] [--no-summary] [--develop | -d] [-u<username>]  [-p<password>]"
    echo ""
    echo "COMMAND OPTIONS:"
    echo "  --help         : Display this help text"
    echo "  --force | -f   : delete the www/openeyes directory without prompting "
    echo "                   - use with caution - useful to refresh an installation,"
    echo "                     or when moving between versions <=1.12 and versions >= 1.12.1"
    echo "  --clean | -ff  : will completely wipe any existing openeyes configuration "
    echo "                   out. This is required when switching between versions <= 1.12 "
    echo "                   from /etc/openeyes - use with caution"
    echo ""
    echo "  --accept	 : Indicate acceptance of the disclaimer without prompting"
    echo ""
    echo "*** NOTE: You can also pass parameters for runcheckout.sh ***"
    echo "           - see $SCRIPTDIR/runcheckout.sh --help"
    exit 1
fi


echo "

Installing openeyes...
"

# Terminate if any command fails
set -e

# Show disclaimer
echo "
DISCLAIMER: OpenEyes is provided under a GNU Affero General Public License v3.0
license and all terms of that license apply
(https://www.gnu.org/licenses/agpl-3.0.html).
Use of the OpenEyes software or code is entirely at your own risk. Neither the
OpenEyes Foundation, ABEHR Digital Ltd or any other party accept any responsibility
for loss or damage to any person, property or reputation as a result of using the
software or code. No warranty is provided by any party, implied or otherwise. This
software and code is not guaranteed safe to use in a clinical environment and
you should make your own assessment on the suitability for such use. Installation
of any openeyes software indicates acceptance of this disclaimer.

"

if [ ! "$accept" = "1" ]; then
		echo "
To continue installing you must accept the disclaimer...
"
		select yn in "Accept" "Decline"; do
			case $yn in
				Accept ) echo "Accepted. Continuing installation...
                "; accept="1"; break;;
				Decline ) echo "Declined. Aborting installation.
          You cannot use this installer without accepting the disclaimer
				"; exit;;
			esac
		done
    else
        echo "        --accept flag detected. Disclaimer was accepted. Continuing...
        "
fi

if [ $force = 1 ]; then
    # If cleanconfig (-ff) has been given on the command line, then completely
    # wipe the existing oe config before continuing. USE WITH EXTREME CAUTION
	if [ $cleanconfig = 1 ]; then
		echo "cleaning old config from /etc/openeyes"
		sudo rm -rf /etc/openeyes 2> /dev/null
        sudo rm -rf $WROOT/protected/config/local/* 2> /dev/null

	fi
    # END of cleanconfig
fi

# Fix permissions
echo "Setting file permissions..."
sudo gpasswd -a "$USER" www-data
sudo chown -R "$USER":www-data .

sudo chmod -R 774 $WROOT
sudo chmod -R g+s $WROOT

# if this isn't a live install, then add the sample DB
if [ $OE_MODE != "LIVE" ]; then checkoutparams="$checkoutparams --sample"; echo "Sample database will be installed."; fi

if [ $nocheckout = 0 ]; then
    echo "calling oe-checkout with $checkoutparams"
    bash $SCRIPTDIR/oe-checkout.sh $checkoutparams
fi

mkdir -p $WROOT/cache
mkdir -p $WROOT/assets
mkdir -p $WROOT/protected/cache
mkdir -p $WROOT/protected/cache/events
mkdir -p $WROOT/protected/files
mkdir -p $WROOT/protected/runtime
mkdir -p $WROOT/protected/runtime/cache
sudo chmod 777 $WROOT/cache
sudo chmod 777 $WROOT/assets
sudo chmod 777 $WROOT/protected/cache
sudo chmod 777 $WROOT/protected/cache/events
sudo chmod 777 $WROOT/protected/files
sudo chmod 777 $WROOT/protected/runtime
sudo chmod 777 $WROOT/protected/runtime/cache

# Ensure we can read/write apache environment variables
grep -q -e "umask 001" /etc/apache2/envvars || sudo -u root bash -c 'echo "umask 001" >> /etc/apache2/envvars'

# Copy sample configuration and fix some file permissions
$SCRIPTDIR/oe-fix.sh --no-compile --no-restart --no-clear --no-assets --no-migrate --no-dependencies --no-eyedraw

# unless the preservedb switch is set add/reset the sample database
if [ $preservedb = 0 ]; then

    resetswitches='--no-migrate --no-fix --banner "New openeyes installation"'

    # If the genetics switch has been set, then enable the genetics module
    [ $genetics = 1 ] && resetswitches='$resetswitches --genetics-enable'

    $SCRIPTDIR/oe-reset.sh $resetswitches

fi

# call oe-fix - unless nocheckout is set, this will also include dependencies
[ $nocheckout = 1 ] && fixparams="--no-dependencies" || fixparams=""
$SCRIPTDIR/oe-fix.sh $fixparams

# unless we are in build mode, configure apache and cron
if [ $OE_MODE != "BUILD" ]; then
    echo Configuring Apache

    echo "
    <VirtualHost *:80>
    ServerName hostname
    DocumentRoot $WROOT
    <Directory $WROOT>
    	Options FollowSymLinks
    	AllowOverride All
    	Order allow,deny
    	Allow from all
    </Directory>
    ErrorLog /var/log/apache2/error.log
    LogLevel warn
    CustomLog /var/log/apache2/access.log combined
    </VirtualHost>
    " | sudo tee /etc/apache2/sites-available/000-default.conf >/dev/null

    sudo service apache2 restart

    # copy cron tasks
    sudo cp -f $SCRIPTDIR/.cron/hotlist /etc/cron.d/
    sudo chmod 0644 /etc/cron.d/hotlist
    sudo cp -f $SCRIPTDIR/.cron/eventimage /etc/cron.d/
sudo chmod 0644 /etc/cron.d/eventimage
fi

echo ""
bash $SCRIPDIR/oe-which.sh

echo --------------------------------------------------
echo OPENEYES SOFTWARE INSTALLED
echo Please check previous messages for any errors
echo --------------------------------------------------
