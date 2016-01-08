<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="invitation")
 * @ORM\HasLifecycleCallbacks()
 */
class Invitation {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="Budget")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id")
     */
    protected $budget;
    
    /**
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;
    
    /**
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     */
    protected $target;
    
    /**
     * @ORM\Column(type="boolean", nullable=TRUE)
     */
    protected $accepted;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;
    
    public function __construct() {
        $this->accepted = false;
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
