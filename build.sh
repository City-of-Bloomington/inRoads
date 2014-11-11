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

wget https://github.com/openlayers/ol3/releases/download/v3.0.0/v3.0.0.zip -O ./build/public/js/vendor/ol3.zip
cd build/public/js/vendor
unzip ol3.zip
mv v3.0.0 ol3
rm ol3.zip
cd ../../../../

tar czvf $DIST/Blossom.tar.gz --transform=s/build/Blossom/ $BUILD
