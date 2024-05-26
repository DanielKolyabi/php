#!/bin/bash
# shellcheck disable=SC2006,SC2034,SC2086
cd "`dirname $0`/../" && clear # set root path
source "./.env" # read .env
source "./app/.env" # read .env

# params
environment="$(if [ $1 ]; then echo ".$1"; fi)"
projectName="--project-name ${APP_NAME}"
dockerCompose="--file app/docker-compose${environment}.yml"
envFile="--env-file .env --env-file app/.env"

# run options
options="${projectName} ${dockerCompose} ${envFile}"

# info
#echo "environment: $environment"
#echo "options: $options"