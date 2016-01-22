<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 22/01/16
 * Time: 18:17
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="sb_payee")
 * @ORM\HasLifecycleCallbacks()
 */
class Payee {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Bill", inversedBy="payees")
     * @ORM\JoinColumn(name="bill_id", referencedColumnName="id")
     */
    protected $bill;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="payees")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;

    /**
     * Payee constructor.
     */
    public function __construct() {

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