FROM debian:trixie-slim

RUN apt-get update && apt-get install -y \
    curl \
    libxml2 \
    unzip

COPY mksaapkg-OS5 /usr/bin/mksapkg
COPY mksaapkg-OS3 /usr/bin/mksapkg-OS3

RUN chmod +x /usr/bin/mksapkg

COPY entrypoint.sh /usr/bin

WORKDIR /data

ENTRYPOINT ["/usr/bin/entrypoint.sh"]