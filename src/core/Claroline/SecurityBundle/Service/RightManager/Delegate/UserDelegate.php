<?php
namespace Claroline\SecurityBundle\Service\RightManager\Delegate;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Doctrine\ORM\EntityManager;
use Claroline\UserBundle\Repository\UserRepository;
use Claroline\SecurityBundle\Service\Exception\RightManagerException;

class UserDelegate implements SubjectDelegateInterface
{
    
    /** @var EntityManager */
    private $em;
    
    /** @var UserRepository */
    private $userRepository;    
    
    
    
    function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->userRepository = $this->em->getRepository('Claroline\UserBundle\Entity\User');
    }

    
    public function buildSecurityIdentity($subject)
    {
        if($subject->getId() == 0)
        {
            throw new RightManagerException(
                "The user must be saved before being granted any right.",
                RightManagerException:: INVALID_USER_STATE
            );
        }
        return UserSecurityIdentity::fromAccount($subject);
    }
    
    public function buildSubject($sid)
    {
        $userName = $sid->getUsername();
        $user = $this->userRepository->findOneByUsername($userName);
        return $user;
    }

}

