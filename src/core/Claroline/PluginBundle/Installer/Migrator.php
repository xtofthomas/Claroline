<?php

namespace Claroline\PluginBundle\Installer;

use Claroline\InstallBundle\Service\BundleMigrator;
use Claroline\PluginBundle\AbstractType\ClarolinePlugin;

class Migrator
{
    private $migrator;
    
    public function __construct(BundleMigrator $migrator) 
    {
        $this->migrator = $migrator;
    }
    
    public function install(ClarolinePlugin $plugin)
    {
        $this->migrator->createSchemaForBundle($plugin);
    }
    
    public function remove(ClarolinePlugin $plugin)
    {
        $this->migrator->dropSchemaForBundle($plugin);
    }
    
    public function migrate(ClarolinePlugin $plugin, $version)
    {        
        $this->migrator->migrateBundle($plugin, $version);
    }  
}