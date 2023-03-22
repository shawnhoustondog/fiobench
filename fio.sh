#! /bin/sh

SCRATCH=/scratch
CONFIGS=/configs
SLEEP=1h

echo ${HOSTNAME}
mkdir -p ${SCRATCH}/${HOSTNAME}

fio $CONFIGS/fio.job --eta=never --directory=${SCRATCH}/${HOSTNAME} --output=${SCRATCH}/fiobench.${HOSTNAME}

sleep ${SLEEP}
