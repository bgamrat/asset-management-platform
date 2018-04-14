<?php

Namespace App\Controller\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Form\Admin\Asset\VendorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of VendorController
 *
 * @author bgamrat
 */
class VendorController extends Controller
{

    /**
     * @Route("/vendor/", name="vendor")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Vendor' );
        $vendors = $repository->findAll();

        return $this->render( 'user/vendor/index.html.twig', array(
                    'vendors' => $vendors,
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

}
