<?php

namespace AppBundle\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\ModelsType;
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
     * @Route("/admin/asset/manufacturer")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $manufacturerForm = $this->createForm( ManufacturerType::class, null, [] );
        $modelsForm = $this->createForm( ModelsType::class, null, [] );

        return $this->render( 'admin/asset/manufacturer.html.twig', array(
                    'manufacturer_form' => $manufacturerForm->createView(),
                    'models_form' => $modelsForm->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

}
