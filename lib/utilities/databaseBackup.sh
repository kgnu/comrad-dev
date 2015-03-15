#!/bin/bash

USER=root
PASS=Bw4xu!gg

BACKUP_PREFIX=/var/backups/database.backup

for i in `seq 5 -1 0`; do
    if [ -f $BACKUP_PREFIX.$i.gz ]; then
        mv $BACKUP_PREFIX.$i.gz $BACKUP_PREFIX.$(($i+1)).gz
    fi
done

mysqldump -u$USER -p$PASS --all-databases | gzip > $BACKUP_PREFIX.0.gz

