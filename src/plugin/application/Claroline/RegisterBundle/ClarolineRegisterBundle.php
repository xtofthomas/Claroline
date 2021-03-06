<?php

namespace Claroline\RegisterBundle;

use Claroline\PluginBundle\AbstractType\ClarolineApplication;
use Claroline\PluginBundle\Widget\ApplicationLauncher;

class ClarolineRegisterBundle extends ClarolineApplication
{
    public function getLaunchers()
    {
        $launcher = new ApplicationLauncher(
            'claroline_register_index', 
            'register.application.name',
            array('ROLE_ANONYMOUS')
        );

        return array($launcher);
    }
    
    public function getRoutingPrefix()
    {
        return 'register';
    }
    
    public function getIndexRoute()
    {
        return 'claroline_register_index';
    }
}