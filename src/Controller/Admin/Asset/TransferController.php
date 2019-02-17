<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Asset\TransferType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of TransferController
 *
 * @author bgamrat
 */
class TransferController extends AbstractController
{

    /**
     * @Route("/admin/asset/transfer", methods={"GET"})
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
