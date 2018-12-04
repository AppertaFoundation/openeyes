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
WROOT="$( cd -P "$SCRIPTDIR/../../../" && pwd )"

MODULEROOT="$WROOT/protected/javamodules"

gitroot=${OE_GITROOT:-'openeyes'}
module=IOLMasterImport

# Check if module is already cloned, if not clone it (will only clone master branch version)
[ -d "$MODULEROOT/${module}" ] && echo "using existing ${module} files" || git -C $MODULEROOT clone ${basestring}/${module}.git $module

# Copy DICOM related files in place as required
if [[ `lsb_release -rs` == "14.04" ]]; then
    # Ubuntu 14.04 uses upstart / init.d
    sudo cp -f $SCRIPTDIR/dicom-file-watcher.conf /etc/init/
else
    # Ubuntu 14.10 and higher uses systemd
    sudo cp -f $SCRIPTDIR/dicom-file-watcher.service /etc/systemd/system/
fi

sed "s|\$SCRIPTDIR|$SCRIPTDIR|; s|\$WROOT|$WROOT|" $SCRIPTDIR/run-dicom-service.sh | sudo tee /usr/local/bin/run-dicom-service.sh &> /dev/null
sudo chmod +x /usr/local/bin/run-dicom-service.sh

sudo bash $SCRIPTDIR/set-cron.sh

sudo id -u iolmaster &>/dev/null || sudo useradd iolmaster -s /bin/false -m
sudo mkdir -p /home/iolmaster/test
sudo mkdir -p /home/iolmaster/incoming
sudo chown iolmaster:www-data /home/iolmaster/*
sudo chmod 775 /home/iolmaster/*

## (re)-link dist directory for IOLMasterImport module and recompile
dwservrunning=0
# first check if service is running - if it is we stop it, then re-start at the end
if ps ax | grep -v grep | grep run-dicom-service.sh > /dev/null
	then
		dwservrunning=1
		echo "Stopping dicom-file-watcher..."
		sudo service dicom-file-watcher stop
fi

sudo rm -rf ${MODULEROOT}/${module}/dist/lib 2>/dev/null || :
sudo mkdir -p ${MODULEROOT}/${module}/dist
sudo ln -s ${MODULEROOT}/${module}/lib ${MODULEROOT}/${module}/dist/lib

# Compile IOLImporter
echo -e "\n\nCompiling ${module}. Please wait....\n\n"
if [ ! sudo ${MODULEROOT}/${module}/compile.sh > /dev/null 2>&1 ]; then echo "COMPILATION FAILURE - Check the logs"; fi

# restart the service if we stopped it, otherwise install the service and start it
if [ $dwservrunning = 1 ]; then
	echo "Restarting dicom-file-watcher..."
	sudo service dicom-file-watcher start
else
  sudo systemctl daemon-reload
  sudo systemctl enable dicom-file-watcher

  sudo systemctl start dicom-file-watcher
fi
