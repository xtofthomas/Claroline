<?php
namespace Claroline\SecurityBundle\Service\RightManager;

use Symfony\Component\Security\Acl\Dbal\AclProvider;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Claroline\SecurityBundle\Service\RightManager\Delegate\TargetDelegateInterface;
use Claroline\SecurityBundle\Service\RightManager\Delegate\SubjectDelegateInterface;
use Claroline\SecurityBundle\Service\RightManager\Delegate\StrategyChooser;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

class RightManager implements RightManagerInterface
{
    /** @var AclProvider */
    private $aclProvider;
    
    /** @var StrategyChooser */
    private $strategyChooser;
    
    /** @var TargetDelegateInterface */
    private $currentTargetStrategy;
    
    /** @var SubjectDelegateInterface */
    private $currentSubjectStrategy;
    
    
    
    
    function __construct(AclProvider $aclProvider, StrategyChooser $strategyChooser)
    {
        $this->aclProvider = $aclProvider;
        $this->strategyChooser = $strategyChooser;
        $this->currentTargetStrategy = null;            
        $this->currentSubjectStrategy = null;
    }    
    
    public function addRight($target, $subject, $mask)
    {
        $this->chooseStrategy($target, $subject);
        
        
        $sid = $this->currentSubjectStrategy->buildSecurityIdentity($subject);        
        $acl = $this->getAclFromTarget($target);  

        $this->currentTargetStrategy->insertAce($acl, $sid, $mask);
        
        $this->aclProvider->updateAcl($acl);
        
    }

    public function removeRight($target, $subject, $mask)
    {
        $this->chooseStrategy($target, $subject);
        $this->doRemoveRight($target, $subject, $mask);
    }
    
    public function removeAllRights($target, $subject)
    {
        $this->chooseStrategy($target, $subject);
        $this->doRemoveRight($target, $subject, 0);
    }
    
    private function doRemoveRight($target, $subject, $mask = 0)
    {
        $sid = $this->currentSubjectStrategy->buildSecurityIdentity($subject);
        $acl = $this->getAclFromTarget($target);  
        
        $this->doRecursiveRemoveRight($acl, $sid, $mask, 0);
        $this->aclProvider->updateAcl($acl);
    }
    
    private function doRecursiveRemoveRight(Acl $acl, $sid, $mask, $startIndex)
    {
        $aces = $this->currentTargetStrategy->getAces($acl);
        if(count($aces) == 0) 
        {
            return;
        }
        if($startIndex < 0)
        {
            return;
        }
        for($aceIndex = $startIndex; $aceIndex < count($aces); ++$aceIndex)
        {
            $ace = $aces[$aceIndex];
            $compatibleAce = 
                $ace->getSecurityIdentity() == $sid
                && $this->isCompatibleMask($ace->getMask(), $mask);
            if ($compatibleAce)
            {
                $currentMask = $ace->getMask();
                $mb = new MaskBuilder($currentMask);
                $mb->remove($mask);
                $updatedMask = $mb->get();     
                if($updatedMask == 0 || $mask == 0)
                {
                    $this->currentTargetStrategy->deleteAce($acl, $aceIndex);
                    $this->doRecursiveRemoveRight($acl, $sid, $mask, $aceIndex);
                    return;
                }
                else
                {
                    
                    $this->currentTargetStrategy->updateAce($acl, $aceIndex, $updatedMask);
                }                
            }            
        }
        
    }
    
    

    public function setRight($target, $subject, $right)
    {       
        $this->chooseStrategy($target, $subject);
        $this->removeAllRights($target, $subject);
        $this->addRight($target, $subject, $right);
    }
    
    public function getUsersWithRight($target, $rightMask)
    {
        
        $this->currentTargetStrategy = 
            $this->strategyChooser->chooseTargetStrategy($target);
        $this->currentSubjectStrategy = 
            $this->strategyChooser->getUserDelegate();
        
        $acl = $this->getAclFromTarget($target);    
        $aces = $this->currentTargetStrategy->getAces($acl);
        
        
        $res = array();
        foreach($aces as $ace)
        {
            $compatibleAce = $this->isCompatibleMask($ace->getMask(), $rightMask);
            if ($compatibleAce)
            {
                $sid = $ace->getSecurityIdentity();
                $res[] = $this->currentSubjectStrategy->buildSubject($sid);
            }
        }
        return $res;        
    }
    
    public function hasRight($target, $subject, $rightMask)
    {
        $this->chooseStrategy($target, $subject);
        $acl = $this->getAclFromTarget($target);
        
        $sid = $this->currentSubjectStrategy->buildSecurityIdentity($subject);

        try
        {
            return $acl->isGranted(array($rightMask), array($sid));
        }
        catch(NoAceFoundException $ex)
        {
            unset($ex);
            return false;
        }
    }
    
    
    private function isCompatibleMask($testedMask, $baseMask)
    {
        return $baseMask == ($baseMask & $testedMask);
    }
    
    private function getAclFromTarget($target)
    {
        $oid = $this->currentTargetStrategy->buildObjectIdentity($target);
        try
        {
            $acl = $this->aclProvider->findAcl($oid);
        }
        catch( AclNotFoundException $ex)
        {
            unset($ex);
            $acl = $this->aclProvider->createAcl($oid); //needed for class acl
        }
        return $acl;
    }
    
    private function chooseStrategy($target = null, $subject = null)
    {
        $this->currentTargetStrategy = $this->strategyChooser->chooseTargetStrategy($target);
        $this->currentSubjectStrategy = $this->strategyChooser->chooseSubjectStrategy($subject);
    }
    
    
}

