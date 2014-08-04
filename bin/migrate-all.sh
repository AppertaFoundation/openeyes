#!/usr/bin/env sh
while [ "$PWD" != "/" ];
do
	if [ -f ./protected/yiic ]; then break;	fi
	cd ..
done
if [ ! -f ./protected/yiic ]; then
	echo "Cannot find yiic"
	exit 1
fi
cd protected
echo "Migrating OpenEyes..."
./yiic migrate --interactive=0
echo "Migrating Modules..."
./yiic migratemodules --interactive=0
echo "Clearing cache..."
rm -rf cache/*
sudo rm -rf ../cache/*
echo "Clearing assets..."
sudo rm -rf ../assets/*
