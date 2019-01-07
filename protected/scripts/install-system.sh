#!/bin/bash

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

# if OE_GITROOT environment variable is set, use it. else default to openeyes
gitroot=${OE_GITROOT:-"openeyes"}

# Process commandline parameters
dependonly=0
showhelp=0

for i in "$@"
do
case $i in
	-d|--depend-only) dependonly=1
		## only (re)install dependencies
		;;
    --help) showhelp=1
    ;;
	*)  if [ ! -z "$i" ]; then
			echo "Unknown command line: $i. Try --help"
		fi
    ;;
esac
done

# Show help text
if [ $showhelp = 1 ]; then
    echo ""
    echo "DESCRIPTION:"
    echo "Installs system dependencies for OpenEyes"
    echo ""
    echo "usage: install-system.sh [--help] [--depend-only | -d]"
    echo ""
    echo "COMMAND OPTIONS:"
    echo "  --help         : Display this help text"
    echo "  --depend-only "
    echo "          | -d   : install/refresh dependencies, but do NOT (re)configure"
	echo ""
    exit 1
fi

function found_error() {
	echo "******************************************"
	echo "*** AN ERROR OCCURRED - CHECK THE LOGS ***"
	echo "******************************************"
	exit 1
}

trap 'found_error' ERR



echo -e "STARTING SYSTEM INSATLL IN MODE: $OE_MODE...\n"

export DEBIAN_FRONTEND=noninteractive

# use minimal amount of memory swapping
sudo sysctl vm.swappiness=10

# update system packages
sudo apt-get update
# workaround grub-pc upgrade not working in noninteractive mode (this can be removed once the issue with the upstream package has been resolved)
sudo apt-mark hold grub-pc
sudo apt-get upgrade -y
sudo apt-get install -y software-properties-common

#add repos for PHP5.6 and Java7
sudo add-apt-repository ppa:ondrej/php -y

echo "Performing package updates"
# ffmpeg 3 isn't supported on xenial or older, so a third party ppa is required
if [[ `lsb_release -rs` == "16.04" ]] || [[ `lsb_release -rs` == "14.04" ]]; then
		sudo add-apt-repository ppa:mc3man/gstffmpeg-keep -y
		sudo add-apt-repository ppa:jonathonf/ffmpeg-3 -y
fi

# Don't worry about upgrading everything for build mode
if [ "$OE_MODE" != "BUILD" ]; then
  sudo apt-get upgrade -y
  sudo apt-get autoremove -y
fi


echo Installing required system packages

# if we are in dev mode, or need to include mysqlserver inside the image, then add additional packages
extrapackages=$OE_INSTALL_EXTRA_PACKAGES
[ "$OE_INSTALL_LOCAL_DB" == "" ] && OE_INSTALL_LOCAL_DB="TRUE" # default to local db unless otherwise specified in env
[ "$OE_INSTALL_LOCAL_DB" == "TRUE" ] && extrapackages="mariadb-server mariadb-client $extrapackages"

# Install required packages + any extras - or if in build or host mode, intstall minimal build packages only
echo "---= installing $OE_MODE packages =---"
if [ "$OE_MODE" == "BUILD" ]; then
	sudo apt-get install -y $(<$SCRIPTDIR/.packages-build.conf)
elif [ "$OE_MODE" == "HOST" ]; then
  sudo apt-get install -y $(<$SCRIPTDIR/.packages-host-only.conf)
else
	sudo apt-get install -y $(<$SCRIPTDIR/.packages.conf) $extrapackages
fi

# If we're not in LIVE mode, then also install the dev tools (required for building openeyes)
[ "$OE_MODE" != "LIVE" ] && bash $SCRIPTDIR/install-dev-tools.sh


# Download and install wkhtmltopdf/toimage (needed for printing and lightning viewer)
# switch to correct wkhtml version based on OS (trusty/xenial/bionic/etc)
echo -e "\n\nInstalling wkhtmltopdf...\n\n"
osver=`lsb_release -rs`
if [[ "$osver" == "14.04" ]]; then
    # Ubuntu 14.04
	sudo wget -O wkhtml.deb https://downloads.wkhtmltopdf.org/0.12/0.12.5/wkhtmltox_0.12.5-1.trusty_amd64.deb
elif [[ "$osver" == "16.04" ]]; then
	# Ubuntu 16.04
	sudo wget -O wkhtml.deb https://downloads.wkhtmltopdf.org/0.12/0.12.5/wkhtmltox_0.12.5-1.xenial_amd64.deb
elif [[ "$osver" == "18.04" ]]; then
	# Ubuntu 18.04
	sudo wget -O wkhtml.deb https://downloads.wkhtmltopdf.org/0.12/0.12.5/wkhtmltox_0.12.5-1.bionic_amd64.deb
fi
## TODO: replace with package manager. e.g, https://packagist.org/packages/h4cc/wkhtmltopdf-amd64 and https://packagist.org/packages/h4cc/wkhtmltoimage-amd64
sudo dpkg -i --force-depends wkhtml.deb || echo -e "\n\nWARNING WARNING WARNING:\n\nUnable to install wkhtmltopdf automatically\nPlease install manually"
sudo rm wkhtml.deb

if [ ! "$dependonly" = "1" ]; then

  # Enable display_errors and error logging for PHP, plus configure timezone
  sudo mkdir /var/log/php 2>/dev/null || :
  sudo chown www-data /var/log/php
  sudo chown www-data /var/log/php
  sudo sed -i "s/^display_errors = Off/display_errors = On/" /etc/php/5.6/apache2/php.ini
  sudo sed -i "s/^display_startup_errors = Off/display_startup_errors = On/" /etc/php/5.6/apache2/php.ini
  sudo sed -i "s|^;date.timezone =|date.timezone = ${TZ:-'Europe/London'}|" /etc/php/5.6/apache2/php.ini
  sudo sed -i "s/;error_log = php_errors.log/error_log = \/var\/log\/php_errors.log/" /etc/php/5.6/apache2/php.ini
  sudo sed -i "s/^display_errors = Off/display_errors = On/" /etc/php/5.6/cli/php.ini
  sudo sed -i "s/^display_startup_errors = Off/display_startup_errors = On/" /etc/php/5.6/cli/php.ini
  sudo sed -i "s/;error_log = php_errors.log/error_log = \/var\/log\/php_errors.log/" /etc/php/5.6/cli/php.ini
  sudo sed -i "s|^;date.timezone =|date.timezone = ${TZ:-'Europe/London'}|" /etc/php/5.6/cli/php.ini

	if [ ! sudo timedatectl set-timezone ${TZ:-'Europe/London'} ]; then
		 sudo ln -sf /usr/share/zoneinfo/${TZ:-Europe/London} /etc/localtime
	fi

	sudo a2enmod rewrite
    ## TODO: Decide a clen way to add bash environment 'fixes'
    # cp /vagrant/install/bashrc /etc/bash.bashrc
    # source /vagrant/install/bashrc

    # Bind mysql to accept connections from remote servers (only if mysql is locally installed)
    if [ "$OE_INSTALL_LOCAL_DB" == "TRUE" ]; then
        sudo sed -i "s/\s*bind-address\s*=\s*127\.0\.0\.1/bind-address    = 0.0.0.0/" /etc/mysql/my.cnf
    	sudo sed -i "s/\s*bind-address\s*=\s*127\.0\.0\.1/bind-address    = 0.0.0.0/" /etc/mysql/mariadb.conf.d/50-server.cnf
        sudo service mysql restart
    fi

	# disable terminal bell on tab / delete errors (just becuase it is really annoying!)
    ## TODO: Move this in to same process as other bash settings
	sudo sed -i "s/# set bell-style none/set bell-style none/" /etc/inputrc
	sudo sed -i "s/# set bell-style visible/set bell-style visible/" /etc/inputrc

fi


# Install php composer if we are not in live mode (not needed in production environments)
if [ "$OE_MODE" != "LIVE" ]; then
    sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    sudo php composer-setup.php
    sudo php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer
fi

# ensure mcrypt has been installed sucesfully
sudo phpenmod mcrypt
sudo phpenmod imagick

echo "updating imagick to read/write PDFs"

#  update ImageMagick policy to allow PDFs
sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick-6/policy.xml &> /dev/null || :
sudo sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick/policy.xml &> /dev/null || :

echo "--------------------------------------------------"
echo "SYSTEM SOFTWARE INSTALLED"
echo "Please check previous messages for any errors"
echo "--------------------------------------------------"
