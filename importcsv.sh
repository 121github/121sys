#!/bin/sh

MYSQL_ARGS="--defaults-file=/etc/mysql/debian.cnf"
DB="$3"
DELIM=","

CSV="$1"
TABLE="$2"

USER="$4"
PASS="$5"

[ "$CSV" = "" -o "$TABLE" = "" ] && echo "Syntax: $0 csvfile tablename" && exit 1

FIELDS=$(head -1 "$CSV" | sed -e 's/'$DELIM'/` varchar(255),\n`/g' -e 's/\r//g')
FIELDS='`'"$FIELDS"'` varchar(255)'

#echo "$FIELDS" && exit

mysql --user="$USER" --password="$PASS" $DB -e "
DROP TABLE IF EXISTS $TABLE;
CREATE TABLE $TABLE ($FIELDS);

LOAD DATA LOCAL INFILE '$(pwd)/$CSV' INTO TABLE $TABLE
FIELDS TERMINATED BY '$DELIM'
OPTIONALLY ENCLOSED BY '\"'
IGNORE 1 LINES
;
"
