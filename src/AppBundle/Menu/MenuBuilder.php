<?php

// src/AppBundle/Menu/MenuBuilder.php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;

class MenuBuilder
{

    private $factory;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct( FactoryInterface $factory )
    {
        $this->factory = $factory;
    }

    public function createMainMenu( array $options )
    {
        $menu = $this->factory->createItem( 'home' );

        $menu->addChild( 'Home', ['route' => 'homepage'] );
        $admin = $menu->addChild( 'Admin' );
        $admin->addChild( 'Groups' );
        $admin->addChild( 'Locations' );
        $user = $admin->addChild( 'User');
        $user->addChild( 'Users', [ 'route' => 'app_web_admin_admin_user'] );
        $user->addChild( 'Invitation', [ 'route' => 'app_web_admin_invitation_invitation'] );
        $menu->addChild('Logout', ['route' => 'fos_user_security_logout']);

        return $menu;
    }

}
