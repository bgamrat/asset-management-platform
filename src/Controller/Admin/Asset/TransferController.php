<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Asset\TransferType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of TransferController
 *
 * @author bgamrat
 */
class TransferController extends Controller
{

    /**
     * @Route("/admin/asset/transfer")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $transferForm = $this->createForm( TransferType::class, null, [] );

        return $this->render( 'admin/asset/transfer.html.twig', array(
                    'transfer_form' => $transferForm->createView()
                ) );
    }

}
