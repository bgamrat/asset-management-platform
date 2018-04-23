<?php

Namespace App\Controller\Api\Admin\Common;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use App\Menu\MenuBuilder;
use App\Menu\JsonRenderer;

class MenuStoreController extends FOSRestController
{
    private $menuBuilder;
    private $jsonRenderer;

    public function __construct (MenuBuilder $menuBuilder, JsonRenderer $jsonRenderer ) {
        $this->menuBuilder = $menuBuilder;
        $this->jsonRenderer = $jsonRenderer;
    }

    /**
     * @View()
     */
    public function getAdminmenuAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $adminMenu = $this->menuBuilder->createAdminMenu( [] );
        $renderer = $this->jsonRenderer;
        return array_values( $renderer->render( $adminMenu ) );
    }

}
