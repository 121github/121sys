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
            'rm -rf application/config/database.php.*',
            'setfacl -R -m u:one2one:rwx -m u:\`whoami\`:rwx datafiles',
            'setfacl -dR -m u:one2one:rwx -m u:\`whoami\`:rwx datafiles',
            'find . -type f -exec chmod 644 {} \;',
            'find . -type d -exec chmod 755 {} \;',
            'chmod -R 777 importcsv.sh'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}