#! /bin/sh

SCRATCH=/scratch
CONFIGS=/configs
OUTPUT=/output
SLEEP=1h

# start up delay...
#sleep 30s

#mkdir -p ${SCRATCH}/${NODE_NAME}/${HOSTNAME}
mkdir ${HOSTNAME}
touch ${OUTPUT}/fiobench.${HOSTNAME}

fio $CONFIGS/fio.job --eta=never --directory=${HOSTNAME} --output=${OUTPUT}/fiobench.${HOSTNAME}

# sleep forever
while true;do sleep 1h;done
