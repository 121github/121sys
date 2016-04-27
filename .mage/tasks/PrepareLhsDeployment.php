<?php

namespace Task;

use Mage\Task\AbstractTask;

class PrepareLhsDeployment extends AbstractTask
{
    public function getName()
    {
        return 'Preparing the LH Surveying deployment on the production environment';
    }

    public function run()
    {
        $commandList = array(
            'mv application/config/database.php.lhsurveying application/config/database.php',
            'mv .htaccess_lhs .htaccess',
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:one2one:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:one2one:rwx -m u:\`whoami\`:rwx datafiles',
            'find . -type f ! -path "./datafiles/*" -exec chmod 644 {} \;',
            'find . -type d ! -path "./datafiles" -exec chmod 755 {} \;',
            'chmod 777 importcsv.sh',
//            'mkdir -p upload/function_triggers',
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}