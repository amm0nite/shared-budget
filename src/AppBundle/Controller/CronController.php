<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 26/01/16
 * Time: 16:39
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Action;
use AppBundle\Entity\Bill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CronController extends Controller
{
    /**
     * @Route("/cron", name="sb_cron")
     */
    public function indexAction() {
        if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
            throw $this->createAccessDeniedException("This script is only accessible from localhost.");
        }

        // find monthly bill names
        $em = $this->getDoctrine()->getManager();
        $montlhies = $em
            ->createQuery('SELECT DISTINCT bi.name, bu.id as budget_id FROM AppBundle:Bill bi JOIN bi.budget bu WHERE bi.monthly=TRUE ORDER BY bi.id DESC')
            ->getResult();

        // put in array indexed by budget id
        $budgetIndexed = array();
        foreach ($montlhies as $montlhy) {
            $budgetId = $montlhy['budget_id'];

            if (!in_array($budgetId, array_keys($budgetIndexed))) {
                $budgetIndexed[$budgetId] = array();
            }

            $budgetIndexed[$budgetId][] = $montlhy['name'];
        }

        // making copies
        $copies = array();
        foreach ($budgetIndexed as $budgetId => $names) {
            $budget = $em->getRepository('AppBundle:Budget')->find($budgetId);

            foreach ($names as $name) {
                $lastBill = $em
                    ->createQuery('SELECT b FROM AppBundle:Bill b WHERE b.name=:name AND b.budget=:budget ORDER BY b.id DESC')
                    ->setParameter('budget', $budget)
                    ->setParameter('name', $name)
                    ->setMaxResults(1)
                    ->getSingleResult();
                /* @var $lastBill Bill */

                $date = $lastBill->getDate();
                $now = new \DateTime();
                $diff = $date->diff($now);
                $monthsPassed = $diff->m + $diff->y * 12;

                if ($monthsPassed > 0) {
                    // copy the bill
                    $bill = Bill::copyFrom($lastBill);
                    $bill->setDate($now);
                    $em->persist($bill);
                    $copies[] = $bill->toArray();

                    $action = Action::copyBill($budget, $bill->toArray());
                    $em->persist($action);
                }
            }
        }

        $em->flush();
        return new JsonResponse($copies);
    }
}