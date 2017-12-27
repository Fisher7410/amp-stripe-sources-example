#!/usr/bin/env bash

printf "Projects root resolved: %s\n" "${PROJECTS_ROOT:?You must set projects root ro continue. Ex: export PROJECTS_ROOT=$(pwd)}"

if hash docker-machine 2>/dev/null
then
    echo -e "\033[1;33mCreating docker-machine instance\033[m"

    docker-machine start

    docker-machine ssh default "sudo mkdir -p ${PROJECTS_ROOT} && \
        sudo mount.nfs -o noacl,async,nolock,vers=3,udp,noatime,actimeo=1 192.168.99.1:${PROJECTS_ROOT} ${PROJECTS_ROOT}"

    eval $(docker-machine env default)
fi

echo -e "\033[1;33mCreating docker-containers\033[m"

docker-compose -p stripe up -d

echo -e "\033[1;32mSystem is up\033[m"

exit