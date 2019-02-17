<?php

Namespace App\Controller\User;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends AbstractController
{

    /**
     * @Route("/calendar", name="calendar")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );

        $today = new \DateTime();

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( ['e.id'] )
                ->from( 'App\Entity\Schedule\Event', 'e' );
        $queryBuilder->where( $queryBuilder->expr()->lt( ':now', 'e.end' ) );
        $queryBuilder->setParameters( ['now' => date( 'Y/m/d' )] );
        $events = $queryBuilder->getQuery()->getResult();
        $ids = array_column($events,'id');
        $events = $em->getRepository( 'App\Entity\Schedule\Event' )->findBy( ['id' => $ids] );

        return $this->render( 'user/calendar/index.html.twig', array(
                    'date' => $today,
                    'events' => $events,
                    'fluid' => true
                ) );
    }

}
