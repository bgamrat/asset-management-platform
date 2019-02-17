<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Asset\VendorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of VendorController
 *
 * @author bgamrat
 */
class VendorController extends AbstractController
{

    /**
     * @Route("/admin/asset/vendor", methods={"GET"})
     * @Route("/admin/asset/vendor/{name}", name="app_admin_asset_vendor_get", methods={"GET"})
     */
    public function indexAction( $name = null )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        if( $name !== null )
        {
            $vendor = $this->getDoctrine()->getEntityManager()->getRepository( 'App\Entity\Asset\Vendor' )->findOneBy( ['name' => $name] );
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
