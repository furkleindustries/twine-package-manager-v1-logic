FROM php:7.1-fpm

ARG DEBIAN_FRONTEND=noninteractive

RUN mkdir -p /etc/twine-package-manager/logic/

WORKDIR /etc/twine-package-manager/logic/

COPY . .

RUN \
    cd /tmp/ && \
    wget https://www.python.org/ftp/python/3.7.0/Python-3.7.0a1.tar.xz && \
    tar xvf Python-3.7.0a1.tar.xz && \
    cd Python3.7.0a1/ && \
    ./configure && \
    make && \
    make install && \
    /etc/twine-package-manager/logic/scripts/installLogicDependencies