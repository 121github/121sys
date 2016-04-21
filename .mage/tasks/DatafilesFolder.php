<?php

namespace Task;

use Mage\Task\AbstractTask;

class DatafilesFolder extends AbstractTask
{
    public function getName()
    {
        return 'Create symlink to the datafiles folder';
    }

    public function run()
    {
        $commandList = array(
            'chmod 777 datafiles',
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}