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
        $menu->addChild( 'Admin' )
                ->addChild( 'User', [ 'route' => 'app_web_admin_admin_user'] )
                    ->addChild( 'Invitation', [ 'route' => 'app_web_admin_invitation_invitation'] );
        // ... add more children

        return $menu;
    }

}
