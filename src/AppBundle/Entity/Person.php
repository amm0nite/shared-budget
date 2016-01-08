<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="person")
 * @ORM\HasLifecycleCallbacks()
 */
class Person {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=32)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=128)
     * @Assert\Email(checkHost=true)
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=64)
     */
    protected $password;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;
    
    /**
     * @ORM\OneToMany(targetEntity="Bill", mappedBy="person")
     */
    protected $bills;

    public function __construct() {
        $this->bills = new ArrayCollection();
    }
    
    /**
     * @Assert\IsFalse(message = "That name is reserved")
     */
    public function isNameReserved() {
        $reservedNames = array('admin', 'administrator', 'operator', 'root', 'mod', 'moderator');
        return in_array($this->name, $reservedNames);
    }
    
    /**
     * @Assert\IsTrue(message = "The password cannot match your name")
     */
    public function isPasswordLegal() {
        return $this->password != $this->name;
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
}
