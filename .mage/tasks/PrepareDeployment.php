<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the production environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.dist application/config/database.php',
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'chmod -R 777 importcsv.sh'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}