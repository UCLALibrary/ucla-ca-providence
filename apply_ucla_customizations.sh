#!/bin/bash

# Directory structure of TARGET is the same as SOURCE, for relevant files and directories.
SOURCE="ucla_customizations"
TARGET="ucla_providence"

# Delete files which come with the vanilla release, but are not needed
# and appear to be build artifacts.
DELETE_FILES=(
    DOCKER_ENV
    docker_tag
    output.log
    .travis.setup.php
    .travis.yml
    Vagrantfile
    Vagrantfile.debian-stretch64
    Vagrantfile.focal
)
for FILE in "${DELETE_FILES[@]}"; do
  if [ -f "${TARGET}/${FILE}" ]; then
    rm "${TARGET}/${FILE}"
  fi
done

# Create media/collectiveaccess, which doesn't exist by default
# but seems to be expected, maybe created during fresh installation only?
# media exists, just not media/collectiveaccess
if [ ! -d "${TARGET}/media/collectiveaccess" ]; then
  mkdir "${TARGET}/media/collectiveaccess"
fi

# Copy UCLA-specific customizations into the main application directory.
cp -pr "${SOURCE}"/* "${TARGET}"/

