<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Common\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Entity\Invitation;

/**
 * Description of DefaultController
 *
 * @author bgamrat
 */
class DefaultController extends Controller
{
    /**
     * @Route("/admin/asset")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
      
        return $this->render( 'admin/asset/index.html.twig' );
    }
}