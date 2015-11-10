#!/bin/sh

MYSQL_ARGS="--defaults-file=/etc/mysql/debian.cnf"
DB="$2"
CSV="$1"

mysql --user=bradf --password=brad123 $DB < $CSV