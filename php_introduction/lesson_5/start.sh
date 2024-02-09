path=`pwd`
project=$1
containerName=$2

cd $path && clear
docker-compose -p $project up -d && clear
docker exec -it $containerName sh && clear
docker-compose -p $project down