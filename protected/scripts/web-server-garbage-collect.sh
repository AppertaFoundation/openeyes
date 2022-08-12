#/bin/bash

seconds=1800
echo $1
# allow seconds to be overidden as a command arg
if [ -n "$1" ]; then seconds=$1; fi

echo "Starting background garbage collection every $seconds"

while [ true ]; do
    sleep $seconds
    echo "Running garbage collection..."
    beforeP="Before-Processes: $(ps waxu | grep chrome | wc -l)"
    beforeF="Before-Files: $(find /tmp -name 'puppeteer_dev_chrome_profile-*' | wc -l)"
    killall --older-than 30m chrome
    find /tmp/puppeteer_dev_chrome_profile-* -mmin +30 -type d -exec rm -rf {} \;
    afterP="After-Processes: $(ps waxu | grep chrome | wc -l)"
    afterF="After-Files: $(find /tmp -name 'puppeteer_dev_chrome_profile-*' | wc -l)"
    echo -e "${beforeP}, ${beforeF}, ${afterP}, ${afterF}" >>/var/log/removePuppeteer.log
done
