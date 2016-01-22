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
     * @var Bill[]
     */
    protected $bills;

    /**
     * @ORM\OneToMany(targetEntity="Bill", mappedBy="payer")
     * @var Bill[]
     */
    protected $payments;

    /**
     * @ORM\ManyToMany(targetEntity="Bill", mappedBy="guests")
     */
    protected $paidBills;

    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="user")
     * @var Invitation[]
     */
    protected $invitationsSent;

    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="target")
     * @var Invitation[]
     */
    protected $invitationsReceived;

    /**
     * @ORM\OneToMany(targetEntity="Budget", mappedBy="user")
     * @var Budget[]
     */
    protected $budgets;

    /**
     * User constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->bills = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->paidBills = new ArrayCollection();
        $this->invitationsSent = new ArrayCollection();
        $this->invitationsReceived = new ArrayCollection();
        $this->budgets = new ArrayCollection();
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

    /**
     * @return Budget[]
     */
    public function getBudgets() {
        $result = array();

        // owned
        foreach ($this->budgets as $b) {
            $result[] = $b;
        }

        // invited
        foreach ($this->invitationsReceived as $i) {
            if ($i->getAccepted()) {
                $result[] = $i->getBudget();
            }
        }

        return $result;
    }

    /**
     * @param Budget $budget
     * @return bool
     */
    public function isInvited(Budget $budget) {
        foreach ($this->invitationsReceived as $i) {
            if ($i->getAccepted() && $i->getBudget()->getId() == $budget->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array(
            'id' => $this->id,
            'username' => $this->username
        );
    }
}
