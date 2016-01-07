<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="person")
 */
class Person {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;
    
    /**
     * @ORM\OneToMany(targetEntity="Bill", mappedBy="person")
     */
    protected $bills;

    public function __construct() {
        $this->bills = new ArrayCollection();
    }
}
