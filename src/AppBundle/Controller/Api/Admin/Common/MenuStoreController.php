<?php

namespace AppBundle\Controller\Api\Admin\Common;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;

class MenuStoreController extends FOSRestController
{
    /**
     * @View()
     */
    public function getAdminmenuAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $adminMenu = $this->get( 'app.menu_builder' )->createAdminMenu( [] );
        $renderer = $this->get( 'app.menu_renderer' );
        return array_values( $renderer->render( $adminMenu ) );
    }

}
