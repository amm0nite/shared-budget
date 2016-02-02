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

        $actions = $this->get('app.history')->getHistory($user);

        if (count($actions) > 0) {
            $user->setLastSeenAction($actions[0]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('activity/index.html.twig', array('actions' => $actions));
    }
}