<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bill")
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
     * @ORM\Column(type="string", length=100)
     * 
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $price;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $description;
    
    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="bills")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;
    
    /**
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="bills")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id")
     */
    protected $budget;

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
    
    public function toArray() {
        return Array(
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description
        );
    }
}
