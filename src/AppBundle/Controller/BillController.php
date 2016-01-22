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

        $form = $this->createForm(BillType::class, $bill);
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
}
