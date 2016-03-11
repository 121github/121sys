#!/bin/bash

# This function is called at the very bottom of the file
main() {
	restart_apache
}

restart_apache() {
	echo "Restarting apache"
	service apache2 restart
}

main
exit 0
