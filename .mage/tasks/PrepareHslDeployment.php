<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareHslDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the Hsl deployment on the production environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.hsl application/config/database.php',
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