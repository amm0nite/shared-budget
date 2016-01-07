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
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;
    
    /**
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;
    
    /**
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     */
    protected $target;
    
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
