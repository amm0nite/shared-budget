<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="sb_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;
    
    /**
     * @ORM\OneToMany(targetEntity="Bill", mappedBy="user")
     */
    protected $bills;

    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="user")
     */
    protected $invitations;

    public function __construct() {
        parent::__construct();
        $this->bills = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }
    
    /**
     * @Assert\IsFalse(message = "That name is reserved")
     */
    public function isUsernameReserved() {
        $reservedNames = array('admin', 'administrator', 'operator', 'root', 'mod', 'moderator');
        return in_array($this->username, $reservedNames);
    }
    
    /**
     * @Assert\IsTrue(message = "The password cannot match your name")
     */
    public function isPasswordLegal() {
        return $this->password != $this->username;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreated() {
        $this->created = new \DateTime();
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function setUpdated() {
        $this->updated = new \DateTime();
    }

    public function getCreated() {
        return $this->created;
    }
}
