# fio-kubernetes
based on: https://joshua-robinson.medium.com/storage-benchmarking-with-fio-in-kubernetes-14cf29dc5375

## create project and build fedora-fio image
oc new-project my-fio-benchmark  
oc create -f fiobench-build.yaml

### for RWO
oc set image-lookup fiobench

## testing RWX
oc create -f fio_RWX_pvc.yaml  
oc rsh deploymentconfig.apps/fiobench-rwx -n my-fio-benchmark

### Scale up
oc scale --replicas=X deployment.apps/fiobench-rwx

## testing RWO
oc create -f fio_RWO_pvc.yaml  
oc rsh deploymentconfig.apps/fiobench-rwo -n my-fio-benchmark

### Scale up
oc scale --replicas=X statefulset.apps/fiobench-rwo

## clean up

### testing RWX
oc delete -f fio_RWX_pvc.yaml

### testing RWO
oc delete -f fio_RWO_pvc.yaml  
oc delete pvc -l app=fiobench-rwo

### all
oc delete -f fiobench-build.yaml

### if you have permissions
oc delete ns my-fio-benchmark
