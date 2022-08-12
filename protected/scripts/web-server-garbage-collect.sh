#/bin/bash

waitseconds=1800
stalemins=10

# allow seconds to be overidden as a command arg
if [ -n "$1" ]; then waitseconds=$1; fi
if [ -n "$2" ]; then stalemins=$2; fi

echo "Starting background garbage collection every $waitseconds seconds"

while [ true ]; do
    sleep $waitseconds
    echo "Running garbage collection..."
    beforeP="Before-Processes: $(ps waxu | grep chrome | wc -l)"
    beforeF="Before-Files: $(find /tmp -name 'puppeteer_dev_chrome_profile-*' | wc -l)"
    killall --older-than 20m chrome
    find /tmp -maxdepth 1 -name "puppeteer_dev_chrome_profile-*" -mmin +$stalemins -type d -exec rm -rf {} \;
    afterP="After-Processes: $(ps waxu | grep chrome | wc -l)"
    afterF="After-Files: $(find /tmp -name 'puppeteer_dev_chrome_profile-*' | wc -l)"
    echo -e "${beforeP}, ${beforeF}, ${afterP}, ${afterF}" >>/var/log/removePuppeteer.log
done
