<?php

namespace Claroline\PluginBundle\Installer\Recorder;

use Claroline\PluginBundle\Installer\Recorder\Writer\ConfigurationFileWriter;
use Claroline\PluginBundle\Installer\Recorder\Writer\DatabaseWriter;
use Claroline\PluginBundle\AbstractType\ClarolinePlugin;

class Recorder
{
    private $configWriter;
    private $dbWriter;
    
    public function __construct(ConfigurationFileWriter $configWriter, DatabaseWriter $dbWriter)
    {
        $this->configWriter = $configWriter;
        $this->dbWriter = $dbWriter;
    }
    
    public function setConfigurationFileWriter(ConfigurationFileWriter $writer)
    {
        $this->configWriter = $writer;
    }
    
    public function setDatabaseWriter(DatabaseWriter $writer)
    {
        $this->dbWriter = $writer;
    }
    
    public function register(ClarolinePlugin $plugin)
    {
        $pluginFQCN = get_class($plugin);
        
        $this->dbWriter->insert($plugin);       
        $this->configWriter->registerNamespace($plugin->getVendorName());
        $this->configWriter->addInstantiableBundle($pluginFQCN);
        $this->configWriter->importRoutingResources(
            $pluginFQCN, 
            $plugin->getRoutingResourcesPaths(),
            $plugin->getRoutingPrefix()
        );
    }
    
    public function unregister(ClarolinePlugin $plugin)
    {
        $pluginFQCN = get_class($plugin);
        
        $this->dbWriter->delete($pluginFQCN);
        $this->configWriter->removeNamespace($plugin->getVendorName());
        $this->configWriter->removeInstantiableBundle($pluginFQCN);
        $this->configWriter->removeRoutingResources($pluginFQCN);
    }
    
    public function isRegistered($pluginFQCN)
    {
        $isSavedInDb = $this->dbWriter->isSaved($pluginFQCN);
        $isSavedInConfig = $this->configWriter->isRecorded($pluginFQCN);
        
        if ($isSavedInDb && $isSavedInConfig)
        {
            return true;
        }
        
        return false;
    }
}