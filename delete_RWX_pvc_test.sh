#! /bin/bash


oc delete DeploymentConfig -l app=fiobench-rwx  
oc delete pvc -l app=fiobench-rwx  

