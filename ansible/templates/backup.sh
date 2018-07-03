#!/bin/bash
# @copyright 2011-2018 City of Bloomington, Indiana
# @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
APPLICATION_NAME="inroads"
MYSQLDUMP="/usr/bin/mysqldump"
MYSQL_DBNAME="{{ inroads_db.name }}"
MYSQL_CREDENTIALS="/etc/cron.daily/backup.d/${APPLICATION_NAME}.cnf"
BACKUP_DIR="{{ inroads_backup_path }}"
SITE_HOME="{{ inroads_site_home }}"

#----------------------------------------------------------
# Backup
# Creates a tarball containing a full snapshot of the data in the site
#----------------------------------------------------------
# How many days worth of tarballs to keep around
num_days_to_keep=5

now=`date +%s`
today=`date +%F`

# Dump the database
$MYSQLDUMP --defaults-extra-file=$MYSQL_CREDENTIALS $MYSQL_DBNAME > $SITE_HOME/$MYSQL_DBNAME.sql
cd $SITE_HOME
tar czf $today.tar.gz $MYSQL_DBNAME.sql
mv $today.tar.gz $BACKUP_DIR

# Purge any backup tarballs that are too old
cd $BACKUP_DIR
for file in `ls`
do
	atime=`stat -c %Y $file`
	if [ $(( $now - $atime >= $num_days_to_keep*24*60*60 )) = 1 ]
	then
		rm $file
	fi
done

# Update the timestamp on a log file
touch /var/log/cron/inroads
