#!/usr/bin/env bash

echo "----------------------------------------"
echo "++++++++++++ UPDATE PROJECT ++++++++++++"

PROJECT_NAME=account

if [ -z ${ENV} ];then
    ENV="dev"
fi

if [ -z ${S3VAR_PATH} ];then
    S3VAR_PATH="$HOME/.zuragan_config/zuragan.$ENV.env"
fi

if [ ! -f ${S3VAR_PATH} ] ; then
    echo "S3 VARIABLE PATH not found, we can not generate requiring file"
fi

echo ">> fetch env file"
env $(sh ${S3VAR_PATH} ${PROJECT_NAME} | xargs) ./etc/script/zuget.sh ${PWD}


echo ">> set permission"
docker-compose exec fpm chmod 600 storage/oauth*
docker-compose exec web chown www-data.www-data storage/oauth*

echo ">> install dependency"
docker-compose exec fpm ./composer.phar install

echo ">> install migration"
docker-compose exec fpm php artisan migrate