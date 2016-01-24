<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 24/01/16
 * Time: 15:01
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ActivityController extends Controller {
    /**
     * @Route("/activity", name="sb_activity")
     */
    public function indexAction() {
        $user = $this->getUser();
        /* @var $user User */
        $budgets = $user->getBudgets();

        $em = $this->getDoctrine()->getManager();
        $actions = $em
            ->createQuery('SELECT a FROM AppBundle:Action a WHERE a.budget IN (:budgets) OR a.user = :user ORDER BY a.created DESC')
            ->setParameter('budgets', $budgets)
            ->setParameter('user', $user)
            ->setMaxResults(10)
            ->getResult();

        return $this->render('activity/index.html.twig', array('actions' => $actions));
    }
}