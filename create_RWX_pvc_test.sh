#! /bin/bash


for i in {0..9}; do
  read -r -d '' YAML[$i] << EOM
---
apiVersion: apps.openshift.io/v1
kind: DeploymentConfig
metadata:
  name: fiobench-rwx-$i
  labels:
    app: fiobench-rwx
spec:
  replicas: 1
  selector:
    app: fiobench-rwx
    deploymentconfig: fiobench-rwx-$i
  strategy:
    resources: {}
  template:
    metadata:
      labels:
        app: fiobench-rwx
        deploymentconfig: fiobench-rwx-$i
    spec:
      containers:
      - name: fiobench-rwx
        image: fiobench:latest
        command: ["fio.sh"]
        volumeMounts:
        - name: fio-config-vol
          mountPath: /configs
        - name: fio-data
          mountPath: /scratch
        - name: webroot-mount
          mountPath: /output
        imagePullPolicy: Always
        resources: {}
      restartPolicy: Always
      volumes:
      - name: fio-config-vol
        configMap:
          name: fio-job-config
      - name: fio-data
        persistentVolumeClaim:
          claimName: fio-claim-$i
      - name: webroot-mount
        persistentVolumeClaim:
          claimName: webroot
  test: false
  triggers:
  - type: ConfigChange
  - imageChangeParams:
      automatic: true
      containerNames:
      - fiobench-rwx
      from:
        kind: ImageStreamTag
        name: fiobench:latest
    type: ImageChange
...
---
kind: PersistentVolumeClaim
apiVersion: v1
metadata:
  name: fio-claim-$i
  labels:
    app: fiobench-rwx
spec:
#  storageClassName: ocs-storagecluster-cephfs
#  storageClassName: ibm-spectrum-scale-light-sc
  storageClassName: fake-fs-sc
  accessModes:
  - ReadWriteMany
  resources:
    requests:
#      storage: 50Ti
      storage: 10Gi
...
EOM
done

for i in ${!YAML[@]}; do
  echo "${YAML[$i]}" | oc create -f -
done

