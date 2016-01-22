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
        $budget = $this->get('app.checker')->budget($this->getUser(), $budget_id);

        $invitation = new Invitation();
        $invitation->setBudget($budget);

        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->setUser($this->getUser());

            $action = new Action();
            $action->setTemplate('invitation_new');
            $action->setBudget($budget);
            $action->setData(array(
                'budget' => $invitation->getBudget()->getId(),
                'user' => $invitation->getUser()->getId(),
                'target' => $invitation->getTarget()->getId()
            ));

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
        $invitation = $this->get('app.checker')->invitation($this->getUser(), $id);
        $budget = $invitation->getBudget();
        $before = $invitation->getStatus();

        $actions = array(
            'cancel' => 'canceled',
            'renew' => 'pending'
        );
        $invitation->setStatus($actions[$action]);

        $action = new Action();
        $action->setTemplate('invitation_update');
        $action->setBudget($budget);
        $action->setData(array(
            'id' => $invitation->getId(),
            'before' => $before,
            'after' => $invitation->getStatus()
        ));

        $em = $this->getDoctrine()->getManager();
        $em->persist($invitation);
        $em->persist($action);
        $em->flush();

        $this->addFlash('notice', $this->get('translator')->trans('invitation.updatesuccessful'));
        return $this->redirectToRoute('sb_budget_show', array('id' => $budget->getId()));
    }
}