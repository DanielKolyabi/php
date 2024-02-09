path=`pwd`
project=$1

cd $path && clear
docker-compose -p $project build