#!/bin/bash
APPNAME=road_closings
DIR=`pwd`
BUILD=$DIR/build

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

# The PHP code does not need to actually build anything.
# Just copy all the files into the build
rsync -rlv --exclude-from=$DIR/buildignore --delete $DIR/ $BUILD/$APPNAME
cd $BUILD
tar czvf $APPNAME.tar.gz $APPNAME
