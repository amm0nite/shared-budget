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
     * @ORM\Column(type="text", nullable=TRUE)
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
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
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
     * @return Bill[]|ArrayCollection
     */
    public function getBills() {
        return $this->bills;
    }

    /**
     * @return Invitation[]|ArrayCollection
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
    public function getMemberUsernames() {
        $result = array();
        $members = $this->getMembers();
        foreach ($members as $member) {
            $result[$member->getId()] = $member->getUsername();
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getDebts() {
        $result = array();

        $balance = $this->getBalance();

        // calculate amount of money that should move
        $sum = 0;
        foreach ($balance as $userId => $userBalance) {
            if ($userBalance > 0) {
                $sum += $userBalance;
            }
        }

        // find people who did not pay enough
        $badGuys = array();
        foreach ($balance as $userId => $userBalance) {
            if ($userBalance < 0) {
                $badGuys[] = $userId;
            }
        }

        // find people who did pay too much
        $goodGuys = array();
        foreach ($balance as $userId => $userBalance) {
            if ($userBalance >= 0) {
                $goodGuys[] = $userId;
            }
        }

        // only people that did not pay enough (badguys)
        // should repay their underpaid amount (abs($badGuyBalance))
        // to each people who paid too much (goodguys)
        // depending how much they over paid ($goodGuyBalance / $sum)
        foreach ($badGuys as $badGuyId) {
            $badGuyBalance = $balance[$badGuyId];
            $result[$badGuyId] = array();

            foreach ($goodGuys as $goodGuyId) {
                $goodGuyBalance = $balance[$goodGuyId];
                $ratio = $goodGuyBalance / $sum;

                $result[$badGuyId][$goodGuyId] = abs($badGuyBalance) * $ratio;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getBalance() {
        $balance = array();
        $detailedBalance = $this->getDetailedBalance();

        foreach ($detailedBalance as $id => $userBalance) {
            $sum = 0;
            foreach ($userBalance as $debt) {
                $sum += $debt['amount'];
            }
            $balance[$id] = $sum;
        }

        return $balance;
    }

    /**
     * @return array
     */
    private function getDetailedBalance() {
        $members = $this->getMembers();
        $balance = array();

        foreach ($members as $member) {
            $balance[$member->getId()] = array();
        }

        foreach ($this->getBills() as $bill) {
            $guests = $bill->getGuests();

            if ($guests->count() > 0) {
                $pid = $bill->getPayer()->getId();
                $part = $bill->getPrice() / $guests->count();

                foreach ($guests as $guest) {
                    $gid = $guest->getId();

                    if ($pid != $gid) {
                        $balance[$gid][] = array('id' => $pid, 'amount' => ($part * -1));
                        $balance[$pid][] = array('id' => $gid, 'amount' => $part);
                    }
                }
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
