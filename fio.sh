#! /bin/sh

SCRATCH=/scratch
CONFIGS=/configs
OUTPUT=/output
SLEEP=1h

# start up delay...
sleep 60s

mkdir -p ${SCRATCH}/${HOSTNAME}

fio $CONFIGS/fio.job --eta=never --directory=${SCRATCH}/${HOSTNAME} --output=${OUTPUT}/fiobench.${HOSTNAME}

# sleep forever
while true;do sleep 1h;done
