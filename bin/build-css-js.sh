#!/bin/bash
./node_modules/.bin/encore production
cd assets/vendor; ./build.sh; cd ../..
tar czf ~/public-build.tgz public/build
