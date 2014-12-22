<?php

namespace ScoutEvent\BaseBundle\Entity;

use ScoutEvent\BaseBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_role")
 */
class UserRole
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
     */
    protected $user;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     */
    private $role;
    
    public function __construct(User $user, $role) {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

}
