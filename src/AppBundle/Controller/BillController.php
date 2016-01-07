<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Bill;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of BillController
 *
 * @author pierre
 */
class BillController extends Controller {
    
    /**
     * @Route("/bill/{id}")
     */
    public function getAction($id) {
       $id = intval($id);
       
       $repo = $this->getDoctrine()->getRepository('AppBundle:Bill');
       $bill = $repo->find($id);
       /* @var $bill Bill */
       
       if (!$bill) {
           throw $this->createNotFoundException();
       }
       
       return new JsonResponse($bill->toArray());
    }
}
