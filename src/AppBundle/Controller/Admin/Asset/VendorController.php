<?php

namespace AppBundle\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\VendorType;
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
     * @Route("/admin/asset/vendor")
     * @Route("/admin/asset/vendor/{name}", name="app_admin_asset_vendor_get")
     * @Method("GET")
     */
    public function indexAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        if( $name !== null )
        {
            $vendor = $this->getDoctrine()->getEntityManager()->getRepository( 'AppBundle\Entity\Asset\Vendor' )->findOneBy( ['name' => $name] );
            $vendorId = $vendor->getId();
        }
        else
        {
            $vendorId = null;
        }

        $form = $this->createForm( VendorType::class, null, [] );

        return $this->render( 'admin/asset/vendor.html.twig', array(
                    'vendor_id' => $vendorId,
                    'vendor_form' => $form->createView()) );
    }

}
