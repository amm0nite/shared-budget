<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 21/01/16
 * Time: 15:51
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Budget;
use AppBundle\Entity\Invitation;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\BudgetType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Action;
use Symfony\Component\HttpFoundation\Response;

class BudgetController extends Controller {
    /**
     * @Route("/budgets", name="sb_budgets")
     */
    public function indexAction() {
        $user = $this->getUser();
        /* @var $user User */
        $budgets = $user->getBudgets();
        $invitations = $user->getPendingInvitationsReceived();
        return $this->render('budget/index.html.twig', array('budgets' => $budgets, 'invitations' => $invitations));
    }

    /**
     * @Route("/budget/new", name="sb_budget_new")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request) {
        $budget = new Budget();
        $form = $this->createForm(BudgetType::class, $budget);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budget->setUser($this->getUser());

            $invitation = new Invitation();
            $invitation->setBudget($budget);
            $invitation->setUser($budget->getUser());
            $invitation->setTarget($budget->getUser());
            $invitation->setStatus('manager');

            $action = Action::newBudget($budget, $this->getUser(), $budget->toArray());

            $em = $this->getDoctrine()->getManager();
            $em->persist($budget);
            $em->persist($invitation);
            $em->persist($action);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('budget.createsuccessful'));
            return $this->redirectToRoute('sb_budgets');
        }

        return $this->render('budget/form.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/budget/{id}/edit", name="sb_budget_edit", requirements={"id": "\d+"})
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $budget = $this->get('app.checker')->budget($this->getUser(), $id);
        $before = $budget->toArray();

        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $action = Action::editBudget($budget, $this->getUser(), $before, $budget->toArray());

            $em = $this->getDoctrine()->getManager();
            $em->persist($budget);
            $em->persist($action);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('budget.editsuccessful'));
            return $this->redirectToRoute('sb_budgets');
        }

        return $this->render('budget/form.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/budget/{id}/delete", name="sb_budget_delete", requirements={"id": "\d+"})
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request, $id) {
        $budget = $this->get('app.checker')->budget($this->getUser(), $id);

        if ($request->query->get('confirm')) {
            $action = Action::deleteBudget($budget, $this->getUser(), $budget->toArray());

            $em = $this->getDoctrine()->getManager();
            $em->remove($budget);
            $em->persist($action);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('budget.deletesuccessful'));
            return $this->redirectToRoute('sb_budgets');
        }
        else {
            return $this->render('budget/delete.html.twig', array('budget' => $budget));
        }
    }

    /**
     * @Route("/budget/{id}", name="sb_budget_show", requirements={"id": "\d+"})
     *
     * @param $id
     * @return Response
     */
    public function showAction($id) {
        $budget = $this->get('app.checker')->budget($this->getUser(), $id, false);

        $repo = $this->getDoctrine()->getRepository('AppBundle:Bill');
        $bills = $repo->findBy(
            array('budget' => $budget),
            array('date' => 'DESC'),
            100
        );

        return $this->render('budget/show.html.twig', array('budget' => $budget, 'bills' => $bills));
    }

    /**
     * @Route("/budget/{id}/repay", name="sb_budget_repay", requirements={"id": "\d+"})
     *
     * @param $id
     * @return Response
     */
    public function repayAction($id) {
        $budget = $this->get('app.checker')->budget($this->getUser(), $id, false);

        $usernames = $budget->getMemberUsernames();
        $debts = $budget->getDebts();

        return $this->render('budget/repay.html.twig', array(
            'budget' => $budget,
            'debts' => $debts,
            'usernames' => $usernames
        ));
    }
}