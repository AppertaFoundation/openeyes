#!/bin/bash

DEBIAN_FRONTEND=noninteractive apt-get install git -y


if [ $# -lt 1 ]
then
        echo "Usage : openeyes_installer.sh BRANCH_NUMBER"
                echo ""
                echo "The available branches are:"
                echo ""
                BRANCH=$(git ls-remote --heads https://github.com/openeyes/OpenEyes.git | awk -F"/" '{print substr($0, index($0, $3))}')
				select ins_bra in $BRANCH;
        do
                echo "install $ins_bra "
                break
        done
else
        $ins_bra = $1
fi

echo ""
echo "I'm now installing OpenEyes branch $ins_bra"

apt-get update
apt-get upgrade -y

DEBIAN_FRONTEND=noninteractive apt-get install chef -y

/usr/bin/git clone -b master --recursive https://github.com/OpenEyes/oe_chef.git
mkdir /var/www
cd /var/www && git clone -b $ins_bra  https://github.com/openeyes/OpenEyes.git openeyes
cd /root/oe_chef
/usr/bin/chef-solo -c solo.rb -j oe.jason


# IP='ifconfig eth0 | awk '/inet addr/{print substr($2,6)}'
echo ""
echo "OpenEyes $branch was successfully installed !!"
echo ""
echo "You can now start using OpenEyes: http://localhost"