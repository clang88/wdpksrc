## Binary Source

Pre-built ARM binaries for WD MyCloud are available from: https://borg.bauerj.eu/

Place the downloaded `borg` binary in the `bin/` directory before building.

## Working with mksapkg docker
`docker build --platform linux/amd64 ../mksapkg-docker/. -t clang88/mksapkg`

You can simply go to the app root and run the `build-w-docker.sh` script to build the application (change the models array if you want to build for more than one model). I only own the WD MyCloud EX2 Ultra, so I only build for that.

**Important!** MyCloud OS5 mksapkg expects the folder name of the app to be exactly the same as what you wrote in your `apkg.rc` under `Package`. This means you need to make sure you keep the folder name in synch with your `apkg.rc`.