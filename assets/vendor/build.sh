dojo/util/buildscripts/build.sh --bin node --profile profiles/app.profile.js -r
find ../../public -type f -name '*.uncompressed.js' -print0 | xargs -0 rm -rdf
find ../../public -type f -name '*.consoleStripped.js' -print0 | xargs -0 rm -rdf
