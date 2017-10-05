<?php
namespace AppBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Translator;
use AppBundle\Entity\User;

class Checker {
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * Checker constructor.
     * @param Registry $registry
     * @param Translator $translator
     */
    public function __construct(Registry $registry, Translator $translator) {
        $this->registry = $registry;
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @param $id
     * @param bool $requireOwnership
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

    /**
     * @param User $user
     * @param $id
     * @param bool $requireOwnership
     * @return Entity\Bill
     */
    public function bill(User $user, $id, $requireOwnership = true) {
        $repo = $this->registry->getRepository('AppBundle:Bill');
        $bill = $repo->find($id);

        if (!$bill) {
            throw new NotFoundHttpException($this->translator->trans('bill.notfound'));
        }

        $budget = $bill->getBudget();
        if (!$budget) {
            throw new NotFoundHttpException($this->translator->trans('budget.notfound'));
        }

        if ($bill->getUser()->getId() != $user->getId()) {
            if ($requireOwnership) {
                throw new AccessDeniedException($this->translator->trans('bill.accessdenied'));
            }
            if (!$user->isInvited($budget)) {
                throw new AccessDeniedException($this->translator->trans('bill.accessdenied'));
            }
        }

        return $bill;
    }

    /**
     * @param User $user
     * @param $id
     * @param bool $requireOwnership
     * @return Entity\Invitation
     */
    public function invitation(User $user, $id, $requireOwnership = true) {
        $repo = $this->registry->getRepository('AppBundle:Invitation');
        $invitation = $repo->find($id);

        if (!$invitation) {
            throw new NotFoundHttpException($this->translator->trans('invitation.notfound'));
        }

        $budget = $invitation->getBudget();
        if (!$budget) {
            throw new NotFoundHttpException($this->translator->trans('budget.notfound'));
        }

        if ($invitation->getUser()->getId() != $user->getId()) {
            if ($requireOwnership) {
                throw new AccessDeniedException($this->translator->trans('invitation.accessdenied'));
            }
            if (!$user->isInvited($budget)) {
                throw new AccessDeniedException($this->translator->trans('invitation.accessdenied'));
            }
        }

        return $invitation;
    }
}