<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareAcceptHslDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the HSL accept environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.accept_hsl application/config/database.php',
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'find . -type f -exec chmod 664 {} \;',
            'find . -type d -exec chmod 775 {} \;',
            'chmod -R 777 importcsv.sh',
            'scp jenkins@10.10.1.15:/var/lib/jenkins/jobs/121Sys/workspace/upload/function_triggers/hsl_accept.js upload/function_triggers/hsl.js'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}