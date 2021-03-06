<?php

namespace Claroline\WorkspaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Claroline\UserBundle\Entity\User;
use Claroline\PluginBundle\Entity\Tool;

/**
 * @ORM\Entity
 * @ORM\Table(name="claro_workspace")
 */
class Workspace
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length="255")
     * @Assert\NotBlank(message="workspace.name_not_blank")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Claroline\PluginBundle\Entity\Tool")
     * @ORM\JoinTable(
     *      name="claro_workspace_tool",
     *      joinColumns={@ORM\JoinColumn(name="workspace_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tool_id", referencedColumnName="id")}
     * )
     */
    protected $tools;

    public function __construct()
    {
        $this->tools = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getTools()
    {
        return $this->tools->toArray();
    }

    public function addTool(Tool $tool)
    {
        $this->tools->add($tool);
    }
}