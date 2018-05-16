#!/bin/bash
#
# @license https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL, see LICENSE

INROADS_PATH={{ inroads_path }}
INROADS_DATA={{ inroads_data }}

BACKUP_CNF=/etc/cron.daily/backup.d/inroads.cnf
BACKUP_DIR=/srv/backups/inroads


#----------------------------------------------------------
# Backups
#----------------------------------------------------------
now=`date +%s`
today=`date +%F`

# Dump the database
mysqldump --defaults-extra-file="${BACKUP_CNF}" "${DB_NAME}" > "${INROADS_DATA}/${DB_NAME}.sql"

cd "${ASM_DATA}"
tar czf $today.tar.gz "${DB_NAME}.sql" media
mv $today.tar.gz "${BACKUP_DIR}"

# Purge any backup tarballs that are too old
cd "${BACKUP_DIR}"
for file in `ls`
do
	atime=`stat -c %Y $file`
	if [ $(( $now - $atime >= $num_days_to_keep*24*60*60 )) = 1 ]
	then
		rm $file
	fi
done
