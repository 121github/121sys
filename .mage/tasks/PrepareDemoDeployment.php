<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareDemoDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the demo environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.demo application/config/database.php',
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'find . -type f -exec chmod 664 {} \;',
            'find . -type d -exec chmod 775 {} \;',
            'chmod -R 777 importcsv.sh'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}