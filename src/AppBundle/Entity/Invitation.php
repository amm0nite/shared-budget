<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sb_invitation")
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
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="invitations")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id")
     */
    protected $budget;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitationsSent")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitationsReceived")
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

    public function setAccepted($accepted) {
        $this->accepted = (bool) $accepted;
    }

    public function getAccepted() {
        return $this->accepted;
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
