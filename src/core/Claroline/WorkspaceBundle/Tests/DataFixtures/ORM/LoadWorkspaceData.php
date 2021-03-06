<?php

namespace Claroline\WorkspaceBundle\Tests\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Claroline\WorkspaceBundle\Entity\Workspace;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class LoadWorkspaceData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface $container */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $admin = $manager->merge($this->getReference('user/admin'));
        $rightManager = $this->container->get('claroline.security.restricted_owner_right_manager');
        $workspaces = array();
        for($i = 0; $i < 10; ++$i)
        {
            $workspace = new Workspace();
            $workspace->setName("test workspace #{$i}");
            $workspaces[] = $workspace;
            $manager->persist($workspace);
        }
        $manager->flush();
        foreach($workspaces as $workspace)
        {            
            $rightManager->addRight($workspace, $admin, MaskBuilder::MASK_OWNER);
        }

    }

    public function getOrder()
    {
        return 11; // the order in which fixtures will be loaded
    }
}