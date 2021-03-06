<?php
namespace Claroline\SecurityBundle\Service\RightManager\Delegate;

use \Exception;

class StrategyChooser
{
    
    /** @var TargetDelegateInterface */
    private $entityDelegate;
    
    /** @var TargetDelegateInterface */
    private $classDelegate;
    
    /** @var SubjectDelegateInterface */
    private $userDelegate;
    
    /** @var SubjectDelegateInterface */
    private $roleDelegate;
    
    
    function __construct($entityDelegate, $classDelegate, $userDelegate, $roleDelegate)
    {
        $this->entityDelegate = $entityDelegate;
        $this->classDelegate = $classDelegate;
        $this->userDelegate = $userDelegate;
        $this->roleDelegate = $roleDelegate;
    }

    
    public function chooseTargetStrategy($target)
    {
        if(is_null($target))
        {
            return null;
        }
        if ( $this->isAnEntity($target)){
            return $this->entityDelegate;
        }
        if ( $this->isAClass($target)){
            return $this->classDelegate;
        }
        throw new Exception("Cannot choose Target Strategy for [{$target}]");
    }
    
    public function chooseSubjectStrategy($subject)
    {
        if(is_null($subject))
        {
            return null;
        }
        if ( $this->isAUser($subject) ){
            return $this->userDelegate;
        }
        if ( $this->isARole($subject) ){
            return $this->roleDelegate;
        }
        throw new Exception("Cannot choose Subject Strategy for [{$subject}]");
    }
    
    public function getEntityDelegate()
    {
        return $this->entityDelegate;
    }

    public function getClassDelegate()
    {
        return $this->classDelegate;
    }

    public function getUserDelegate()
    {
        return $this->userDelegate;
    }

    public function getRoleDelegate()
    {
        return $this->roleDelegate;
    }

    
    private function isAnEntity($target)
    {
        return is_object($target);
    }
    
    private function isAClass($target)
    {
        return is_string($target) && class_exists($target, false);
    }
    
    private function isAUser($subject)
    {
        return $subject instanceof \Claroline\UserBundle\Entity\User;
    }
    
    private function isARole($subject)
    {
        return $subject instanceof \Claroline\SecurityBundle\Entity\Role;
    }

    
}

