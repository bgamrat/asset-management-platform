<?php

Namespace App\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;
use App\Menu\MenuBuilder;
use App\Menu\JsonRenderer;
use Symfony\Component\Security\Core\Security;

class MenuStoreController extends FOSRestController {

    private $menuBuilder;
    private $jsonRenderer;
    private $security;

    public function __construct(MenuBuilder $menuBuilder, JsonRenderer $jsonRenderer, Security $security) {
        $this->menuBuilder = $menuBuilder;
        $this->jsonRenderer = $jsonRenderer;
        $this->security = $security;
    }

    /**
     * @View()
     * @Route("/api/menu")
     */
    public function getMenuAction(Request $request) {
        $renderer = $this->jsonRenderer;
        $menu = [];
        $menu['user'] = [];
        if ($this->security->isGranted('ROLE_USER')) {
            $menu['user'] = $renderer->render($this->menuBuilder->createUserMenu([]));
        }
        $menu['admin'] = [];
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $menu['admin'] = $renderer->render($this->menuBuilder->createAdminMenu([]));
        }
        $menu['account'] = $renderer->render($this->menuBuilder->createAccountMenu([]));
        $menu['super_admin'] = [];
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $menu['super_admin'] = $renderer->render($this->menuBuilder->createSuperAdminMenu([]));
        }
        return $menu;
    }

}
