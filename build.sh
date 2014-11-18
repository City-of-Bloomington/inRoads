#!/bin/bash
BUILD=./build
DIST=./dist

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

if [ ! -d $DIST ]
	then mkdir $DIST
fi

rsync -rlv --exclude-from=./buildignore --delete ./ ./build/

wget https://github.com/openlayers/openlayers/archive/master.zip -O ./build/public/js/vendor/openlayers.zip
cd build/public/js/vendor
unzip openlayers.zip
mv openlayers-master openlayers
rm openlayers.zip
cd ../../../../

tar czvf $DIST/Blossom.tar.gz --transform=s/build/Blossom/ $BUILD
