# Working with mksapkg docker
* First build the docker image if not yet done:
`docker build --platform linux/amd64 ../mksapkg-docker/. -t clang88/mksapkg`

* Run the docker container like below from the repo root. It should contain an `app` folder with a subfolder that contains the bin, www, apkg.rc, apkg.xml and *.sh files. **Important!** MyCloud OS5 mksapkg expects the folder name to be exactly the same as what you wrote in your `apkg.rc` under `Package`. This means inside the `app`-Folder you should have only one folder with the exact name written in `apkg.rc`. There should be no other folders in that directory.

`docker run --rm -v ./app:/data/ -v ./build:/build -e MODEL=MyCloudEX2Ultra clang88/mksapkg:latest`

This will add the built binary to `./build` on the root of your repo.

**Correct output:**

```bash
BorgBackup/
BorgBackup/preinst.sh
BorgBackup/.DS_Store
BorgBackup/bin/
BorgBackup/bin/borg
BorgBackup/init.sh
BorgBackup/install.sh
BorgBackup/www/
BorgBackup/www/BorgBackup/
BorgBackup/www/BorgBackup/BorgBackup.png
BorgBackup/apkg.sign
BorgBackup/clean.sh
BorgBackup/remove.sh
BorgBackup/stop.sh
BorgBackup/apkg.xml
BorgBackup/start.sh
BorgBackup/apkg.rc
============================================
        mksapkg version: 2.0
============================================
```
**Incorrect output:**
```bash
data/
data/preinst.sh
data/.DS_Store
data/bin/
data/bin/borg
data/init.sh
data/install.sh
data/www/
data/www/BorgBackup/
data/www/BorgBackup/BorgBackup.png
data/apkg.sign
data/clean.sh
data/remove.sh
data/stop.sh
data/build/
data/apkg.xml
data/start.sh
data/apkg.rc
============================================
        mksapkg version: 2.0
============================================
```