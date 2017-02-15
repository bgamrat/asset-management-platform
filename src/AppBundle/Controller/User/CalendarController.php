<?php

namespace AppBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends Controller
{

    /**
     * @Route("/calendar", name="calendar")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );

        $today = new \DateTime();
        $daysOfTheWeek = [];
        $day = new \DateTime( 'last Sunday' );
        $oneDay = new \DateInterval( 'P1D' );
        for( $i = 0; $i < 7; $i++ )
        {
            $daysOfTheWeek[] = $day;
            $day->add( $oneDay );
        }

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( ['e'] )
                ->from( 'AppBundle\Entity\Schedule\Event', 'e' )
                ->join( 'e.client', 'c' )
                ->orderBy( 'e.start,c.name');
    
        $events = $queryBuilder->getQuery()->getResult();

        return $this->render( 'user/calendar/index.html.twig', array(
                    'date' => $today,
                    'days_of_the_week' => $daysOfTheWeek,
                    'events' => $events
        ) );
    }

}
