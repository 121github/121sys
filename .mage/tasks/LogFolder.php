<?php

namespace Task;

use Mage\Task\AbstractTask;

class LogFolder extends AbstractTask
{
    public function getName()
    {
        return 'Create symlink to the log folder';
    }

    public function run()
    {
        $commandList = array(
            'mkdir -p ../logs',
            'chmod 777 ../logs',
            //'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx ../upload',
            //'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx ../upload',
            'ln -nsf ../logs application/logs'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}