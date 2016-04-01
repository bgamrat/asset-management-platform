<?php

// src/AppBundle/Menu/MenuBuilder.php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Security;

class MenuBuilder implements ContainerAwareInterface
{

    use ContainerAwareTrait;

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
        $menu->setChildrenAttribute( 'class', 'nav navbar-nav' );

        $menu->addChild( 'Home', ['route' => 'homepage'] )
                ->setAttribute( 'icon', 'fa fa-home' );
        if( !$this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $menu->addChild( 'Log in', ['route' => 'fos_user_security_login'] )
                    ->setAttribute( 'icon', 'fa fa-login' );
        }
        return $menu;
    }

    public function createUserMenu( array $options )
    {

        $menu = $this->factory->createItem( 'user' );
        $menu->setChildrenAttribute( 'class', 'nav navbar-nav navbar-right' );

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $username = $user->getUsername();
            $menu->addChild( 'User', array('label' => 'Hi ' . $username) )
                    ->setAttribute( 'dropdown', true )
                    ->setAttribute( 'icon', 'fa fa-user' );
            $menu['User']->addChild( 'Edit profile', array('route' => 'homepage') )
                    ->setAttribute( 'icon', 'fa fa-edit' );
            $menu['User']->addChild( 'Logout', ['route' => 'fos_user_security_logout'] );
        }

        return $menu;
    }

    public function createAdminMenu( array $options )
    {
        $menu = $this->factory->createItem( 'home' );

        $menu->addChild( 'Home', ['route' => 'homepage'] );
        $admin = $menu->addChild( 'Admin' );
        $admin->addChild( 'Groups' );
        $admin->addChild( 'Locations' );
        $user = $admin->addChild( 'User' );
        $user->addChild( 'Users', [ 'route' => 'app_web_admin_admin_user'] );
        $user->addChild( 'Invitation', [ 'route' => 'app_web_admin_invitation_invitation'] );
        $menu->addChild( 'Logout', ['route' => 'fos_user_security_logout'] );

        return $menu;
    }

}
