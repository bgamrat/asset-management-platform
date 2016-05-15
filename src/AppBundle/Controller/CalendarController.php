<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends Controller
{
    /**
     * @Route("/calendar", name="calendar")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );
        
        $today = new \DateTime();
        $daysOfTheWeek = [];
        $sunday = new \DateTime('last Sunday');
        $oneDay = new \DateInterval('P1D');
        for ($i = 0; $i < 7; $i++) {
            $daysOfTheWeek[] = $sunday->format('l');
            $sunday->add($oneDay);
        }
        
        return $this->render('calendar/index.html.twig', array(
            'date' => $today,
            'days_of_the_week' => $daysOfTheWeek,
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
   
}
