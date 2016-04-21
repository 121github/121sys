<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareTestDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the test environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.test application/config/database.php',
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'find . -type f ! -path "./datafiles/*" -exec chmod 644 {} \;',
            'find . -type d ! -path "./datafiles" -exec chmod 755 {} \;',
            'chmod 777 importcsv.sh'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}