<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 21/01/16
 * Time: 18:36
 */
namespace AppBundle;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Translator;
use AppBundle\Entity\User;

class Checker {
    private $registry;

    public function __construct(Registry $registry, Translator $translator) {
        $this->registry = $registry;
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @param $id
     * @return Entity\Budget
     */
    public function budget(User $user, $id, $requireOwnership = true) {
        $repo = $this->registry->getRepository('AppBundle:Budget');
        $budget = $repo->find($id);

        if (!$budget) {
            throw new NotFoundHttpException($this->translator->trans('budget.notfound'));
        }

        if ($budget->getUser()->getId() != $user->getId()) {
            if ($requireOwnership) {
                throw new AccessDeniedException($this->translator->trans('budget.accessdenied'));
            }
            if (!$user->isInvited($budget)) {
                throw new AccessDeniedException($this->translator->trans('budget.accessdenied'));
            }
        }

        return $budget;
    }
}