<?php

namespace Claroline\PluginBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Claroline\PluginBundle\Exception\ApplicationException;

class ApplicationRepository extends EntityRepository
{
    public function getIndexApplication()
    {
        $dql = 'SELECT a FROM Claroline\PluginBundle\Entity\Application a '
            . 'WHERE a.isPlatformIndex = true';
        
        try
        {
            $app = $this->_em->createQuery($dql)->getSingleResult();
            
            return $app;
        }
        catch (NoResultException $ex)
        {
            return false;
        }
        catch (NonUniqueResultException $ex)
        {
            throw new ApplicationException(
                'Multiples application are set as platform index targets.',
                ApplicationException::MULTIPLES_INDEX_APPLICATIONS
            );
        }
    }
    
    public function getConnectionTargetApplication()
    {
        $dql = 'SELECT a FROM Claroline\PluginBundle\Entity\Application a '
            . 'WHERE a.isConnectionTarget = true';
        
        try
        {
            $app = $this->_em->createQuery($dql)->getSingleResult();
            
            return $app;
        }
        catch (NoResultException $ex)
        {
            return false;
        }
        catch (NonUniqueResultException $ex)
        {
            throw new ApplicationException(
                'Multiples application are set as connection targets.',
                ApplicationException::MULTIPLES_CONNECTION_TARGET_APPLICATIONS
            );
        }
    }
}