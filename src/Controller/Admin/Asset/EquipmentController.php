<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Asset\AssetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AssetController
 *
 * @author bgamrat
 */
class EquipmentController extends AbstractController
{

    /**
     * @Route("/admin/asset/equipment")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $assetForm = $this->createForm( AssetType::class, null, [] );

        $id = $request->get( 'id' );

        return $this->render( 'admin/asset/equipment.html.twig', [ 'id' => $id,
                    'asset_form' => $assetForm->createView()] );
    }

}
