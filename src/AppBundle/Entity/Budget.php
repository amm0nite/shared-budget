<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="sb_budget")
 * @ORM\HasLifecycleCallbacks()
 */
class Budget {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=64)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $description;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;
    
    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;
    
    /**
     * @ORM\OneToMany(targetEntity="Bill", mappedBy="budget")
     */
    protected $bills;

    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="budget")
     */
    protected $invitations;

    /**
     * @ORM\OneToMany(targetEntity="Action", mappedBy="budget")
     */
    protected $actions;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="budgets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    public function __construct() {
        $this->bills = new ArrayCollection();
        $this->invitations = new ArrayCollection();
        $this->actions = new ArrayCollection();
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

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * @return Bill[]
     */
    public function getBills() {
        return $this->bills;
    }

    /**
     * @return Invitation[]
     */
    public function getInvitations() {
        return $this->invitations;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getMembers() {
        $members = new ArrayCollection();
        $members->add($this->getUser());
        foreach ($this->getInvitations() as $invitation) {
            $members->add($invitation->getTarget());
        }
        return $members;
    }

    /**
     * @return array
     */
    public function getBalance() {
        $members = $this->getMembers();
        $balance = array();
        foreach ($members as $member) {
            $balance[$member->getId()] = 0;
        }

        foreach ($this->getBills() as $bill) {
            $guests = $bill->getGuests();
            if ($guests->count() > 0) {
                $part = $bill->getPrice() / $guests->count();
                foreach ($guests as $guest) {
                    $balance[$guest->getId()] -= $part;
                }
                $balance[$bill->getPayer()->getId()] += $bill->getPrice();
            }
        }

        return $balance;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array(
            'name' => $this->getName(),
            'description' => $this->getDescription()
        );
    }
}
