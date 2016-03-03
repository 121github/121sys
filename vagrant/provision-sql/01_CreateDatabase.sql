# Add any SQL to this directory that should execute as part of provisioning server
#
# Files in the provision-sql directory are executed in alphabetical order, so prefixing
# script names with a number is recommended


# For example, could create development database, setup base schema, or import data
# CREATE database development;

CREATE USER '121sys'@'%' IDENTIFIED BY '121sys';

CREATE database 121sys;
GRANT ALL PRIVILEGES ON 121sys.* TO '121sys'@'%';

CREATE database uk_postcodes;
GRANT ALL PRIVILEGES ON uk_postcodes.* TO '121sys'@'%';

FLUSH PRIVILEGES;