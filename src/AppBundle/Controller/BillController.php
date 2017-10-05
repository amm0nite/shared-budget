<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Bill;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\BillType;
use AppBundle\Entity\Action;

class BillController extends Controller {
    
    /**
     * @Route("/budget/{budget_id}/bill/new", name="sb_bill_new", requirements={"budget_id": "\d+"})
     *
     * @param Request $request
     * @param $budget_id
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, $budget_id) {
        $budget = $this->get('app.checker')->budget($this->getUser(), $budget_id, false);

        $bill = new Bill();
        $bill->setBudget($budget);
        $bill->setPayer($this->getUser());

        if (!$bill->getDate()) {
            $bill->setDate(new \DateTime());
        }

        $members = $budget->getMembers();
        if ($bill->getGuests()->isEmpty()) {
            $bill->setGuests($members);
        }

        $query = $request->query;
        if ($query->has('name') && $query->has('price') && $query->has('payer') && $query->has('payee')) {
            $bill->reimbursement($query->get('name'), $query->get('price'), $query->get('payer'), $query->get('payee'));
        }

        $form = $this->createForm(BillType::class, $bill, array(
            'members' => $members
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bill->setUser($this->getUser());

            $action = Action::newBill($budget, $this->getUser(), $bill->toArray());

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
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $bill = $this->get('app.checker')->bill($this->getUser(), $id, false);
        $budget = $bill->getBudget();
        $before = $bill->toArray();

        $form = $this->createForm(BillType::class, $bill, array(
            'members' => $budget->getMembers()
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $action = Action::editBill($budget, $this->getUser(), $before, $bill->toArray());

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
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id) {
        $bill = $this->get('app.checker')->bill($this->getUser(), $id, false);
        $budget = $bill->getBudget();

        $action = Action::deleteBill($budget, $this->getUser(), $bill->toArray());

        $em = $this->getDoctrine()->getManager();
        $em->remove($bill);
        $em->persist($action);
        $em->flush();

        $this->addFlash('notice', $this->get('translator')->trans('bill.deletesuccessful'));
        return $this->redirectToRoute('sb_budget_show', array('id' => $budget->getId()));
    }
}
