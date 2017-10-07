# Use the official PHP-FPM 7.1 image as a base.
# https://github.com/docker-library/php/blob/4c0766729088fa5c37d46ccd837386f0e91a33ac/7.1/fpm/Dockerfile
FROM php7.1-fpm

# Give full colors in the output.
ARG TERM=xterm-256color

# Make the terminal non-interactive.
ARG DEBIAN_FRONTEND=noninteractive

# Recursively create the directory for the logic container.
RUN mkdir -p /etc/twine-package-manager/logic/

# Set the working directory.
WORKDIR /etc/twine-package-manager/logic/

# Copy the entire current host directory to the container's working directory.
COPY . .

# Run the following command with /bin/sh -c.
RUN \
    ### PYTHON INSTALL \
    # Update the local list of apt repositories. \
    apt-get update && \
    # Install the packages needed to compile and install Python 3.7. \
    apt-get install -y \
        # Do not install recommended packages so as to reduce build time. \
        --no-install-recommends \
        # Do not install suggested packages so as to reduce build time. \
        --no-install-suggests \
        # Needed for Python's _ctypes. \
        libffi-dev \
        # Needed to download source archive. \
        wget \
        # Needed for zlib in Python. \
        zlib1g-dev && \
    \
    # Store the source in the temporary folder. \
    cd /tmp/ && \
    \
    # Download the source archive from Python's ftp. \
    wget https://www.python.org/ftp/python/3.7.0/Python-3.7.0a1.tar.xz && \
    \
    # Extract the source archive into the temporary folder. \
    tar xvf Python-3.7.0a1.tar.xz && \
    # Enter the extracted source directory. \
    cd Python-3.7.0a1/ && \
    \
    # Configure the Python 3.7 source. \
    ./configure && \
    # Build the Python 3.7 source. \
    make && \
    # Install the Python 3.7 source. \
    make install && \
    \
    # Back out of the source directory. \
    cd .. && \
    # Remove the entire source directory. \
    rm -rf Python-3.7.0a1* && \
    ### END PYTHON INSTALL \
    \
    ### CONFIGURATION \
    /etc/twine-package-manager/logic/scripts/installLogicDependencies \
    ### END CONFIGURATION