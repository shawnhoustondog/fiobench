# fio-kubernetes
based on: https://joshua-robinson.medium.com/storage-benchmarking-with-fio-in-kubernetes-14cf29dc5375

## create project and build fedora-fio image
oc new-project my-fio-benchmark  
oc create -f fiobench-build.yaml
#oc create -f web-results.yaml
oc create -f configs.yaml

### for RWO
oc set image-lookup fiobench

## testing RWX
oc create -f fio_RWX_pvc.yaml  
oc rsh deploymentconfig.apps/fiobench-rwx

### Scale up
oc scale --replicas=0 deploymentconfig.apps/fiobench-rwx

## testing RWO
oc create -f fio_RWO_pvc.yaml  
oc rsh statefulset.apps/fiobench-rwo

### Scale up
oc scale --replicas=0 deploymentconfig.apps/fiobench-rwo

## clean up

### testing RWX
oc delete -f fio_RWX_pvc.yaml

### testing RWO
oc delete -f fio_RWO_pvc.yaml  
oc delete pvc -l app=fiobench-rwo

### all
oc delete -f configs.yaml
#oc delete -f web-results.yaml
oc delete -f fiobench-build.yaml


### if you have permissions
oc delete ns my-fio-benchmark
