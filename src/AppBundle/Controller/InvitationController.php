<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\InvitationType;
use AppBundle\Entity\Action;

class InvitationController extends Controller {
    /**
     * @Route("/budget/{budget_id}/invitation/new", name="sb_invitation_new", requirements={"budget_id": "\d+"})
     */
    public function newAction(Request $request, $budget_id) {
        $budget = $this->get('app.checker')->budget($this->getUser(), $budget_id, false);

        $invitation = new Invitation();
        $invitation->setBudget($budget);

        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->setUser($this->getUser());

            $action = Action::newInvitation($budget, $this->getUser(), $invitation->toArray());

            $em = $this->getDoctrine()->getManager();
            $em->persist($invitation);
            $em->persist($action);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('invitation.createsuccessful'));
            return $this->redirectToRoute('sb_budget_show', array('id' => $budget->getId()));
        }

        return $this->render('invitation/form.html.twig', array('form' => $form->createView(), 'budget' => $budget));
    }

    /**
     * @Route("/invitation/{id}/{action}", name="sb_invitation_update", requirements={"id": "\d+", "action": "cancel|renew"})
     */
    public function updateAction($id, $action) {
        $invitation = $this->get('app.checker')->invitation($this->getUser(), $id, false);
        $budget = $invitation->getBudget();
        $before = $invitation->toArray();
        $target = $invitation->getTarget();

        $actionToStatus = array(
            'cancel' => 'canceled',
            'renew' => 'pending'
        );
        $invitation->setStatus($actionToStatus[$action]);

        $action = Action::updateInvitation($budget, $this->getUser(), $before, $invitation->toArray());

        $em = $this->getDoctrine()->getManager();
        $em->persist($invitation);
        $em->persist($action);
        $em->flush();

        $this->addFlash('notice', $this->get('translator')->trans('invitation.updatesuccessful'));

        if ($this->getUser()->getId() != $target->getId()) {
            return $this->redirectToRoute('sb_budget_show', array('id' => $budget->getId()));
        }
        else {
            return $this->redirectToRoute('sb_budgets');
        }
    }

    /**
     * @Route("/invitation/{id}/{action}", name="sb_invitation_answer", requirements={"id": "\d+", "action": "accept|refuse"})
     */
    public function answerAction($id, $action) {
        $invitation = $this->get('app.checker')->invitation($this->getUser(), $id, false);
        $budget = $invitation->getBudget();
        $before = $invitation->toArray();

        $actionToStatus = array(
            'accept' => 'accepted',
            'refuse' => 'refused'
        );
        $invitation->setStatus($actionToStatus[$action]);

        $action = Action::updateInvitation($budget, $this->getUser(), $before, $invitation->toArray());

        $em = $this->getDoctrine()->getManager();
        $em->persist($invitation);
        $em->persist($action);
        $em->flush();

        $this->addFlash('notice', $this->get('translator')->trans('invitation.answersuccessful'));
        return $this->redirectToRoute('sb_budgets');
    }
}