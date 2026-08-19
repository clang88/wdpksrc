#!/bin/bash
#    Entrypoint to build the application. 
#    Copyright (C) 2023 Brad Koehn
#    Updated 2026-08-19 by clang88
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as
#    published by the Free Software Foundation, either version 3 of the
#    License, or (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <https://www.gnu.org/licenses/>.
set -e

if [ -z ${MODEL+x} ]; then
  MODEL="MyCloudEX2Ultra"
fi

# Enters the first directory in the current working directory, which is expected to be the build directory. - Expects only one directory to be present!
cd */
CURRENT_PATH=$(pwd)

/usr/bin/mksapkg -E -s -m "$MODEL"

echo "Checking parent dir for generated binary..."
cd ..
ls -la
echo "Moving generated binary to app directory ($CURRENT_PATH) in docker..."
mv *.bin* "$CURRENT_PATH"