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
     * @ORM\Column(type="string", length=64)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $budgetname;

    /**
     * @ORM\Column(type="text")
     */
    protected $data;

    /**
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="actions")
     * @ORM\JoinColumn(name="budget_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $budget;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="actions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
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
     * @param array $data
     */
    private function setData(array $data) {
        $this->data = json_encode($data);
    }

    /**
     * @return array
     */
    public function getData() {
        return json_decode($this->data, true);
    }

    public function setTemplate($template) {
        $this->template = $template;
    }

    public function getTemplate() {
        return $this->template;
    }

    /**
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * @param Budget $budget
     */
    public function setBudget(Budget $budget) {
        $this->budget = $budget;
        $this->budgetname = $budget->getName();
    }

    /**
     * @return Budget
     */
    public function getBudget() {
        return $this->budget;
    }

    public function getBudgetname() {
        return $this->budgetname;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
        $this->username = $user->getUsername();
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    public function getUsername() {
        return $this->username;
    }

    public static function newBill(Budget $budget, User $user, array $bill) {
        $action = new Action();
        $action->setTemplate('bill_new');
        $action->setBudget($budget);
        $action->setUser($user);
        $action->setData($bill);
        return $action;
    }

    public static function editBill(Budget $budget, User $user, array $before, array $after) {
        $action = new Action();
        $action->setTemplate('bill_edit');
        $action->setBudget($budget);
        $action->setUser($user);

        $differences = array();

        $keys = array('name', 'description', 'price', 'date');
        foreach ($keys as $key) {
            if ($before[$key] != $after[$key]) {
                $differences[$key] = array($before[$key], $after[$key]);
            }
        }

        if ($before['payer']['id'] != $after['payer']['id']) {
            $differences['payer'] = array($before['payer']['username'], $after['payer']['username']);
        }

        $beforeGuestIds = array();
        foreach ($before['guests'] as $guest) {
            $beforeGuestIds[] = $guest['id'];
        }
        $afterGuestIds = array();
        foreach ($after['guests'] as $guest) {
            $afterGuestIds[] = $guest['id'];
        }

        $added = array();
        foreach ($after['guests'] as $guest) {
            if (!in_array($guest['id'], $beforeGuestIds)) {
                $added[] = $guest['username'];
            }
        }
        $removed = array();
        foreach ($before['guests'] as $guest) {
            if (!in_array($guest['id'], $afterGuestIds)) {
                $removed[] = $guest['username'];
            }
        }

        if (count($added) > 0) {
            $differences['added_guests'] = $added;
        }
        if (count($removed) > 0) {
            $differences['removed_guests'] = $removed;
        }

        $data = $after;
        $data['differences'] = $differences;
        $action->setData($data);
        return $action;
    }

    public static function deleteBill(Budget $budget, User $user, array $bill) {
        $action = new Action();
        $action->setTemplate('bill_delete');
        $action->setBudget($budget);
        $action->setUser($user);
        $action->setData($bill);
        return $action;
    }

    public static function newBudget(Budget $budget, User $user, array $bud) {
        $action = new Action();
        $action->setTemplate('budget_new');
        $action->setBudget($budget);
        $action->setUser($user);
        $action->setData($bud);
        return $action;
    }

    public static function editBudget(Budget $budget, User $user, array $before, array $after) {
        $action = new Action();
        $action->setTemplate('budget_edit');
        $action->setBudget($budget);
        $action->setUser($user);

        $differences = array();
        $keys = array('name', 'description');
        foreach ($keys as $key) {
            if ($before[$key] != $after[$key]) {
                $differences[$key] = array($before[$key], $after[$key]);
            }
        }

        $data = $after;
        $data['differences'] = $differences;
        $action->setData($data);
        return $action;
    }

    public static function deleteBudget(Budget $budget, User $user, array $bud) {
        $action = new Action();
        $action->setTemplate('budget_delete');
        $action->setBudget($budget);
        $action->setUser($user);
        $action->setData($bud);
        return $action;
    }

    public static function newInvitation(Budget $budget, User $user, array $invitation) {
        $action = new Action();
        $action->setTemplate('invitation_new');
        $action->setBudget($budget);
        $action->setUser($user);
        $action->setData($invitation);
        return $action;
    }

    public static function updateInvitation(Budget $budget, User $user, array $before, array $after) {
        $action = new Action();
        $action->setTemplate('invitation_update');
        $action->setBudget($budget);
        $action->setUser($user);

        $differences = array();
        if ($before['status'] != $after['status']) {
            $differences['status'] = array($before['status'], $after['status']);
        }

        $data = $after;
        $data['differences'] = $differences;
        $action->setData($data);
        return $action;
    }
}