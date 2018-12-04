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

sudo systemctl daemon-reload
sudo systemctl enable dicom-file-watcher

sudo systemctl start dicom-file-watcher
