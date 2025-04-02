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
  rm "${TARGET}/${FILE}"
done

# Copy UCLA-specific customizations into the main application directory.
cp -pr "${SOURCE}"/* "${TARGET}"/
