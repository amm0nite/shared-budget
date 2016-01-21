<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 21/01/16
 * Time: 15:51
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Budget;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\BudgetType;

class BudgetController extends Controller {
    /**
     * @Route("/budgets", name="sb_budgets")
     */
    public function indexAction() {
        $budgets = $this->getUser()->getBudgets();
        return $this->render('budget/index.html.twig', array('budgets' => $budgets));
    }

    /**
     * @Route("/budget/new", name="sb_budget_new")
     */
    public function createAction() {
        $budget = new Budget();
        $form = $this->createForm(BudgetType::class, $budget);

        return $this->render('budget/new.html.twig', array('form' => $form->createView()));
    }
}