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
     * @var Budget $budget
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
     * @param Budget $budget
     */
    public function setBudget(Budget $budget) {
        $this->budget = $budget;
    }

    /**
     * @return Budget
     */
    public function getBudget() {
        return $this->budget;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $target
     */
    public function setTarget(User $target) {
        $this->target = $target;
    }

    /**
     * @return User
     */
    public function getTarget() {
        return $this->target;
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

    /**
     * @return array
     */
    public function toArray() {
        return array(
            'id' => $this->id,
            'user' => $this->getUser()->toArray(),
            'target' => $this->getTarget()->toArray(),
            'budget' => $this->getBudget()->toArray()
        );
    }
}
