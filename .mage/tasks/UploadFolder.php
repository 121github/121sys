<?php

namespace Task;

use Mage\Task\AbstractTask;

class UploadFolder extends AbstractTask
{
    public function getName()
    {
        return 'Create symlink to the upload folder';
    }

    public function run()
    {
        $commandList = array(
            'mkdir -p ../upload',
            'setfacl -R -m u:www-data:rwx -m u:\`whoami\`:rwx upload',
            'setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx upload',
            'ln -nsf ../upload upload'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}