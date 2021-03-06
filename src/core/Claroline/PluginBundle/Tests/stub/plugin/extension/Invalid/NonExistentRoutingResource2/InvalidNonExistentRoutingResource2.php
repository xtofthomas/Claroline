<?php

namespace Invalid\NonExistentRoutingResource2;

use Claroline\PluginBundle\AbstractType\ClarolineExtension;

class InvalidNonExistentRoutingResource2 extends ClarolineExtension
{
    public function getRoutingResourcesPaths()
    {
        $ds = DIRECTORY_SEPARATOR;
        $existent = __DIR__ . "{$ds}Resources{$ds}config{$ds}routing.yml";
        $nonExistent =  __DIR__ . "{$ds}fake_routing.yml";

        return array($existent, $nonExistent);
    }
}