<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Bill;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\BillType;
use AppBundle\Entity\Action;

/**
 * Description of BillController
 *
 * @author pierre
 */
class BillController extends Controller {
    
    /**
     * @Route("/budget/{budget_id}/bill/new", name="sb_bill_new", requirements={"budget_id": "\d+"})
     */
    public function newAction(Request $request, $budget_id) {
        $budget = $this->get('app.checker')->budget($this->getUser(), $budget_id, false);

        $bill = new Bill();
        $bill->setBudget($budget);

        $members = $budget->getMembers();
        if ($bill->getGuests()->isEmpty()) {
            $bill->setGuests($members);
        }

        $form = $this->createForm(BillType::class, $bill, array(
            'members' => $members
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bill->setUser($this->getUser());

            $action = new Action();
            $action->setTemplate('bill_new');
            $action->setBudget($budget);
            $action->setData($bill->toArray());

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->persist($action);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('bill.createsuccessful'));
            return $this->redirectToRoute('sb_budget_show', array('id' => $budget->getId()));
        }

        return $this->render('bill/form.html.twig', array('form' => $form->createView(), 'budget' => $budget));
    }

    /**
     * @Route("/bill/{id}/edit", name="sb_bill_edit", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, $id) {
        $bill = $this->get('app.checker')->bill($this->getUser(), $id);
        $budget = $bill->getBudget();
        $before = $bill->toArray();

        $form = $this->createForm(BillType::class, $bill, array(
            'members' => $budget->getMembers()
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $action = new Action();
            $action->setTemplate('bill_edit');
            $action->setBudget($budget);
            $action->setData(array(
                'before' => $before,
                'after' => $bill->toArray()
            ));

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->persist($action);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('bill.editsuccessful'));
            return $this->redirectToRoute('sb_budget_show', array('id' => $budget->getId()));
        }

        return $this->render('bill/form.html.twig', array('form' => $form->createView(), 'budget' => $budget));
    }

    /**
     * @Route("/bill/{id}/delete", name="sb_bill_delete", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, $id) {
        $bill = $this->get('app.checker')->bill($this->getUser(), $id);
        $budget = $bill->getBudget();

        $action = new Action();
        $action->setTemplate('bill_delete');
        $action->setBudget($budget);
        $action->setData($bill->toArray());

        $em = $this->getDoctrine()->getManager();
        $em->remove($bill);
        $em->persist($action);
        $em->flush();

        $this->addFlash('notice', $this->get('translator')->trans('budget.deletesuccessful'));
        return $this->redirectToRoute('sb_budget_show', array('id' => $budget->getId()));
    }
}
