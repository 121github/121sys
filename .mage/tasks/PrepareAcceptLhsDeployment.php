<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareAcceptLhsDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the deployment on the LH Surveying accept environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.accept_lhs application/config/database.php',
            'mv .htaccess_lhs .htaccess',
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:one2one:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:one2one:rwx -m u:\`whoami\`:rwx datafiles',
            'find . -type f -exec chmod 664 {} \;',
            'find . -type d -exec chmod 775 {} \;',
            'chmod -R 777 importcsv.sh',
            'mkdir -p upload/function_triggers',
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}