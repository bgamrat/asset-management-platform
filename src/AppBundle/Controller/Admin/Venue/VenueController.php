<?php

namespace AppBundle\Controller\Admin\Venue;

use AppBundle\Form\Admin\Venue\VenueType;
use AppBundle\Form\Admin\Venue\ContractType;
;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of AssetController
 *
 * @author bgamrat
 */
class VenueController extends Controller
{

    /**
     * @Route("/admin/venue/venue")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $form = $this->createForm( VenueType::class, null, [] );

        return $this->render( 'admin/venue/venue.html.twig', array(
                    'venue_form' => $form->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

}