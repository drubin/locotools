#!/bin/bash

FOO=$(cat membership | sed s/\ /-/g)

for a in $FOO; do

	NAME=$(echo $a | awk -F "," '{print $1}')
	DATE=$(echo $a | awk -F "," '{print $2}')
	YEAR=$(echo $DATE | awk -F "-" '{print $1}')

	echo $YEAR
done
