#!/bin/bash
DIR=`pwd`
BUILD=$DIR/build
DIST=$DIR/dist

OPENLAYERS=public/js/vendor/ol3

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

if [ ! -d $DIST ]
	then mkdir $DIST
fi

# The PHP code does not need to actually build anything.
# Just copy all the files into the build
rsync -rlv --exclude-from=$DIR/buildignore --delete $DIR/ $BUILD/

# Build the OpenLayers javascript library
if [ ! -d $BUILD/$OPENLAYERS ]
    then mkdir -p $BUILD/$OPENLAYERS
fi
cd $DIR/$OPENLAYERS
./build.py build
cd $DIR
rsync -rlv $DIR/$OPENLAYERS/build/ $BUILD/$OPENLAYERS/build/

# Create a distribution tarball of the build
tar czvf $DIST/Blossom.tar.gz --transform=s/build/Blossom/ $BUILD
