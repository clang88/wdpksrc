FROM debian:bullseye-slim

RUN apt-get update && apt-get install -y \
    curl libxml2 unzip

COPY MyCloudOS5_mksapkg /usr/bin/mksapkg

RUN chmod +x /usr/bin/mksapkg

COPY entrypoint.sh /usr/bin

WORKDIR /data

ENTRYPOINT ["/usr/bin/entrypoint.sh"]
