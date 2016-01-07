<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LuckyNumberController extends Controller {

  /**
  * @Route("/lucky/numbers/{count}")
  */
  public function numberAction($count) {
    $numbers = array();
    for ($i=0; $i<$count; $i++) {
      $numbers[] = rand(0, 100);
    }
    $numberList = implode(', ', $numbers);
    dump($numberList);

    return $this->render(
      'lucky/numbers.html.twig',
      array('numberList' => $numberList)
    );
  }
}

?>
