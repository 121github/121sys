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
            'find . -type f ! -path "./datafiles/*" -exec chmod 644 {} \;',
            'find . -type d ! -path "./datafiles" -exec chmod 755 {} \;',
            'chmod 777 importcsv.sh',
            'scp jenkins@10.10.1.15:/var/lib/jenkins/jobs/121Sys/workspace/upload/function_triggers/hsl.js upload/function_triggers/hsl.js'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}