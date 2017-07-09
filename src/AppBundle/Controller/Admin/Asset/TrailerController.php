<?php

namespace AppBundle\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\TrailerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of TrailerController
 *
 * @author bgamrat
 */
class TrailerController extends Controller
{

    /**
     * @Route("/admin/asset/trailer/index", name="app_admin_asset_trailer_index")
     * @Method("GET")
     */
    public function adminIndexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $trailerForm = $this->createForm( TrailerType::class, null, [] );

        return $this->render( 'admin/asset/trailers.html.twig', array(
                    'trailer_form' => $trailerForm->createView()
                ) );
    }

}
