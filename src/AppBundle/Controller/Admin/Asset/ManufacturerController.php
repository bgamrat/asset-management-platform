<?php

namespace AppBundle\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\ModelType;
use AppBundle\Form\Admin\Asset\ManufacturerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of AssetController
 *
 * @author bgamrat
 */
class ManufacturerController extends Controller
{

    /**
     * @Route("/admin/asset/manufacturers")
     * @Method("GET")
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
     * @Route("/admin/asset/manufacturer/{mname}/brand/{bname}")
     * @Method("GET")
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
