<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Asset\ModelType;
use App\Form\Admin\Asset\ManufacturerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AssetController
 *
 * @author bgamrat
 */
class ManufacturerController extends AbstractController
{

    /**
     * @Route("/admin/asset/manufacturers", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $manufacturerForm = $this->createForm( ManufacturerType::class, null, [] );

        return $this->render( 'admin/asset/manufacturer.html.twig', array(
                    'manufacturer_form' => $manufacturerForm->createView()
                ) );
    }

    /**
     * @Route("/admin/asset/manufacturer/{mname}/brand/{bname}", methods={"GET"})
     */
    public function getManufacturerBrandAction( $mname, $bname )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $modelForm = $this->createForm( ModelType::class, null, [] );

        return $this->render( 'admin/asset/brand.html.twig', array(
                    'manufacturer_name' => $mname,
                    'brand_name' => $bname,
                    'model_form' => $modelForm->createView()
                ) );
    }

}
