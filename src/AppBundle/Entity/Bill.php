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
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(max=64)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    protected $price;
    
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bills")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="payments")
     * @ORM\JoinColumn(name="payer_id", referencedColumnName="id")
     */
    protected $payer;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="paidBills")
     * @ORM\JoinTable(name="sb_guest")
     */
    protected $guests;


    /**
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="bills")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id")
     */
    protected $budget;

    /**
     * Bill constructor.
     */
    public function  __construct() {
        $this->guests = new ArrayCollection();
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

    public function toArray() {
        return Array(
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description
        );
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
