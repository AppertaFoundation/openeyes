#!/bin/bash
# Find fuill folder path where this script is located, then find root folder
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
    DIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
# Determine root folder for site - all relative paths will be built from here
SCRIPTDIR="$(cd -P "$(dirname "$SOURCE")" && pwd)"
WROOT="$(cd -P "$SCRIPTDIR/../../" && pwd)"

declare -a foldersToDelete=(
    "$WROOT/protected/files"
    "$WROOT/node_modules"
    "$WROOT/protected/modules/eyedraw"
    "$WROOT/protected/runtime/cache"
    "$WROOT/assets"
)

# loop through the list of folders to delete
for i in "${foldersToDelete[@]}"; do
    echo "deleting $i"
    sudo rm -rf "$i" 
done
