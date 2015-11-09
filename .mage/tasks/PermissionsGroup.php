<?php

namespace Task;

use Mage\Task\AbstractTask;

class PermissionsGroup extends AbstractTask
{
    public function getName()
    {
        return 'Changing the files group to www-data';
    }

    public function run()
    {
        $command = 'chgrp -R www-data .';
        $result = $this->runCommandRemote($command);

        return $result;
    }
}