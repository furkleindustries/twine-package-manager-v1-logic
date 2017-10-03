FROM php:7.1-fpm

ARG DEBIAN_FRONTEND=noninteractive

RUN mkdir -p /etc/twine-package-manager/logic/

WORKDIR /etc/twine-package-manager/logic/

COPY . .

RUN \
    apt-get update && \
    apt-get install -y --no-install-recommends python3 && \
    scripts/installLogicDependencies