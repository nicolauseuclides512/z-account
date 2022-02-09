# Set the base image to Ubuntu
FROM ubuntu:16.04

MAINTAINER Jehan Afwazi A <jehan@ontelstudio.com>

RUN apt-get clean && apt-get -y update && apt-get install -y locales curl nano software-properties-common git \
  && locale-gen en_US.UTF-8
RUN apt-get update --fix-missing

# Install nginx
RUN apt-get install -y nginx curl git zip libxrender1

# Copy a configuration file from the current directory
COPY config/default /etc/nginx/sites-available/default

RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log
#RUN ln -sf /dev/stdout /var/www/storage/logs/lumen.log

# copy config sh
ADD config/run.sh /

RUN chmod +x /run.sh

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
# running config

CMD /run.sh