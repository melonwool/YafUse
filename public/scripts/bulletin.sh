#!/bin/bash
while :
do
curl 'http://gm.api.juaiwan.com/actbulletin/sendqueue' 2>&1 > /dev/null
sleep 60
done