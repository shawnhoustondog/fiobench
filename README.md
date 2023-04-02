# fio-kubernetes
based on: https://joshua-robinson.medium.com/storage-benchmarking-with-fio-in-kubernetes-14cf29dc5375

## create project and build fedora-fio image
oc new-project my-fio-benchmark  
oc create -f fiobench-build.yaml  
oc create -f web-results.yaml  
oc apply -f configs.yaml  

oc rsh deploymentconfig.apps/web-results

## testing RWX
oc create -f fio_RWX_pvc.yaml  

### Scale up
oc scale --replicas=0 deploymentconfig.apps/fiobench-rwx  
oc rsh deploymentconfig.apps/fiobench-rwx  

## testing RWX with one PV per DeployementConfig
ls -1 RWX_PVCs/*| xargs -L 1 oc create -f  
// this one is dangerous, but much faster (parallel)  
ls -1 RWX_PVCs/*| xargs -L 1 oc delete --wait=false -f  


## testing RWO
oc set image-lookup fiobench  
oc create -f fio_RWO_pvc.yaml  
oc rsh statefulset.apps/fiobench-rwo  

### Scale up
oc scale --replicas=0 StatefulSet.apps/fiobench-rwo  
oc delete pvc -l app=fiobench-rwo  

## clean up

### RWX
oc delete -f fio_RWX_pvc.yaml  

### RWO
oc delete -f fio_RWO_pvc.yaml  
oc delete pvc -l app=fiobench-rwo  

### all
oc delete -f configs.yaml  
oc delete -f web-results.yaml  
oc delete -f fiobench-build.yaml  


### if you have permissions
oc delete ns my-fio-benchmark  

## Changing fio.job config file
oc scale --replicas=0 deploymentconfig.apps/fiobench-rwx  
oc scale --replicas=0 deploymentconfig.apps/web-results  
  
Then scale back up...  
