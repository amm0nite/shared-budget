<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="sb_action")
 * @ORM\HasLifecycleCallbacks()
 */
class Action {
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
    protected $template;

    /**
     * @ORM\Column(type="text")
     */
    protected $data;

    /**
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="actions")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id")
     */
    protected $budget;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected $updated;

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

    public function setData(array $data) {
        $this->data = json_encode($data);
    }

    public function setTemplate($template) {
        $this->template = $template;
    }

    public function getTemplate() {
        return $this->template;
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
}