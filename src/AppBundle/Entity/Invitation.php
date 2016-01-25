<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="sb_invitation", uniqueConstraints={@ORM\UniqueConstraint(name="unq_budget_user", columns={"budget_id", "target_id"})})
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"budget","target"}, message="This user is already invited")
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
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Budget $budget
     */
    protected $budget;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitationsSent")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitationsReceived")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $target;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=64)
     */
    protected $status;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;
    
    public function __construct() {
        $this->status = 'pending';
    }

    public function getId() {
        return $this->id;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
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
            'budget' => $this->getBudget()->toArray(),
            'status' => $this->getStatus()
        );
    }
}
