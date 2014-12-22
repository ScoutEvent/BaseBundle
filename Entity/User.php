<?php

namespace ScoutEvent\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;
    
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    protected $lastLogin;
    
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"ALL"})
     */
    protected $roles;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get username (email)
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set hashed password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    
    /**
     * Set plain password, hashing it with the salt first
     *
     * @param string $plainPassword
     * @param EncoderFactory $encoderFactory   $this->get('security.encoder_factory')
     * @return User
     */
    public function setRawPassword($plainPassword, $encoderFactory)
    {
        //encrypt user password
        $encoder = $encoderFactory->getEncoder($this);
        $salt = $this->getSalt();
        $password = $encoder->encodePassword($plainPassword, $salt);

        if (!$encoder->isPasswordValid($password, $plainPassword, $salt)) {
            throw new \Exception('Password incorrectly encoded during user registration');
        } else {
            $this->setPassword($password);
        }
        
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        $roles = array();
        foreach ($this->roles as $role) {
            $roles[] = $role->getRole();
        }
        return $roles;
    }

    public function getRawRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = array();
        foreach ($roles as $role) {
            $this->roles[] = new UserRole($this, $role);
        }
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    
    public function __toString()
    {
        return $this->email;
    }
    
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->email !== $user->getEmail()) {
            return false;
        }
        
        $roles = $user->getRoles();
        if (count($this->roles) != count($roles)) {
            return false;
        }
        foreach ($this->roles as $role) {
            if (!in_array($role, $roles)) {
                return false;
            }
        }

        return true;
    }

}
