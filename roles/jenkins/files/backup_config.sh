#!/bin/bash
today=`date +%Y-%m-%d`
backup_to="~/job-archiv-${today}.tar.gz"
find /var/lib/jenkins/ -name "config.xml" -print0 | xargs -0t tar -zcvf ${backup_to}
echo "Backup created in ${backup_to}"
