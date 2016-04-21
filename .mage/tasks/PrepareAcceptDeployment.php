<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareAcceptDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the accept environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.accept application/config/database.php',
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx datafiles',
            'find . -type f -exec chmod 644 {} \;',
            'find . -type d -exec chmod 755 {} \;',
            'chmod -R 777 importcsv.sh',
			'chmod 777 importcsv.sh',
            'scp jenkins@10.10.1.15:/var/lib/jenkins/jobs/121Sys/workspace/upload/function_triggers/ghs.js upload/function_triggers/ghs.js'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}