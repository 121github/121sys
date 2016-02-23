<?php

namespace Task;

use Mage\Task\AbstractTask;

class LhsFunctionTriggers extends AbstractTask
{
    public function getName()
    {
        return 'Moving the function triggers for LH Surveying';
    }

    public function run()
    {
        $commandList = array(
            'scp -P2020 upload/function_triggers/lhs.js one2one@www.leadcontrol.co.uk:www/121sys_lhs/upload/function_triggers/lhs.js'
        );

        $command = implode(" && ", $commandList);

        $result = $this->runCommandRemote($command);

        return $result;
    }
}