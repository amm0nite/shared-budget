<?php
namespace AppBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Entity\User;
use AppBundle\Entity\Action;

class History {
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry) {
        $this->registry = $registry;
    }

    /**
     * @param User $user
     * @param int $limit
     * @return Action[]
     */
    public function getHistory(User $user, $limit = 10) {
        $em = $this->registry->getManager();

        $actions = $em
            ->createQuery('SELECT a FROM AppBundle:Action a WHERE a.budget IN (:budgets) OR a.user = :user ORDER BY a.id DESC')
            ->setParameter('budgets', $user->getBudgets())
            ->setParameter('user', $user)
            ->setMaxResults($limit)
            ->getResult();

        return $actions;
    }

    /**
     * @param User $user
     * @return int
     */
    public function getUnseen(User $user) {
        if (!$user->getLastSeenAction()) {
            return 0;
        }

        $em = $this->registry->getManager();

        $count = $em
            ->createQuery('SELECT COUNT(a) FROM AppBundle:Action a WHERE a.budget IN (:budgets) AND a.user != :user AND a.id > :last')
            ->setParameter('budgets', $user->getBudgets())
            ->setParameter('user', $user)
            ->setParameter('last', $user->getLastSeenAction()->getId())
            ->getSingleScalarResult();

        return $count;
    }
}
