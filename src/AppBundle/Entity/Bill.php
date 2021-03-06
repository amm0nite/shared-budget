<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="sb_bill")
 * @ORM\HasLifecycleCallbacks()
 */
class Bill
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(max=64)
     */
    protected $name;

    /**
     * @var double
     *
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    protected $price;
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=TRUE)
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $monthly;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bills")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="payments")
     * @ORM\JoinColumn(name="payer_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $payer;

    /**
     * @var array|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="paidBills")
     * @ORM\JoinTable(name="sb_guest")
     */
    protected $guests;


    /**
     * @var Budget
     *
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="bills")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $budget;

    /**
     * Bill constructor.
     */
    public function  __construct() {
        $this->guests = new ArrayCollection();
        $this->monthly = false;
    }

    public function getId() {
        return $this->id;
    }
    
    public function setName($name) {
        $this->name = $name;

        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }

    public function setDate(\DateTime $date) {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
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
     * @param User $payer
     */
    public function setPayer(User $payer) {
        $this->payer = $payer;
    }

    /**
     * @return User
     */
    public function getPayer() {
        return $this->payer;
    }

    /**
     * @param User[]|ArrayCollection $guests
     */
    public function setGuests(ArrayCollection $guests) {
        $this->guests = $guests;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getGuests() {
        return $this->guests;
    }

    /**
     * @param boolean $bool
     */
    public function setMonthly($bool) {
        $this->monthly = (bool) $bool;
    }

    /**
     * @return boolean
     */
    public function getMonthly() {
        return $this->monthly;
    }

    /**
     * @return array
     */
    public function toArray() {
        $result = array(
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'date' => $this->getDate()->getTimestamp(),
            'payer' => $this->getPayer()->toArray(),
            'guests' => array()
        );
        foreach ($this->getGuests() as $guest) {
            $result['guests'][] = $guest->toArray();
        }
        return $result;
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function removeDuplicateGuests() {
        // symfony forms with ManyToMany does weird stuff
        // lets filter duplicated guests
        // no information is lost

        $seen = array();
        $newGuests = new ArrayCollection();
        $removed = array();
        foreach ($this->getGuests() as $guest) {
            if (!in_array($guest->getId(), $seen)) {
                $seen[] = $guest->getId();
                $newGuests[] = $guest;
            }
            else {
                $removed[] = $guest->getId();
            }
        }
        $this->setGuests($newGuests);
    }

    /**
     * @param Bill $other
     * @return Bill
     */
    public static function copyFrom(Bill $other) {
        $bill = new Bill();
        $bill->name = $other->name;
        $bill->description = $other->description;
        $bill->price = $other->price;
        $bill->date = $other->date;
        $bill->monthly = $other->monthly;
        $bill->user = $other->user;
        $bill->payer = $other->payer;
        $bill->guests = $other->guests;
        $bill->budget = $other->budget;
        return $bill;
    }

    /**
     * @param $name
     * @param $price
     * @param $payerId
     * @param $payeeId
     */
    public function reimbursement($name, $price, $payerId, $payeeId) {
        $this->name = $name;
        $this->price = $price;

        foreach ($this->getBudget()->getMembers() as $member) {
            if ($member->getId() == $payerId) {
                $this->payer = $member;
            }
            else if ($member->getId() == $payeeId) {
                $guests = new ArrayCollection();
                $guests->add($member);
                $this->guests = $guests;
            }
        }
    }
}
