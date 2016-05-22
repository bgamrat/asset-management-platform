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

    public function createAdminMenu( array $options )
    {
        $menu = $this->factory->createItem( 'admin', [ 'label' => 'common.admin'] );

        $menu->addChild( 'admin', ['route' => 'root', 'label' => 'common.home'] )
                ->setExtra( 'translation_domain', 'AppBundle' );
        $menu->addChild( 'assets', ['label' => 'common.assets'] );
        $menu['assets']->addChild( 'assets', ['label' => 'common.assets', 'route' => 'app_admin_asset_index'] );
        $menu['assets']->addChild( 'manufacturers', ['label' => 'asset.manufacturers', 'route' => 'app_admin_asset_manufacturer_index'] );
        $menu['assets']->addChild( 'vendors', ['label' => 'asset.vendors', 'route' => 'app_admin_asset_vendor_index'] );
        
        $menu->addChild( 'user', ['label' => 'common.users'] );
        $menu['user']->addChild( 'users', ['label' => 'common.users', 'route' => 'app_admin_user_index'] );
        $menu['user']->addChild( 'invitations', ['label' => 'user.invitation', 'route' => 'app_admin_user_invitation_index'] );
        $menu->addChild( 'logout', ['label' => 'common.log_out', 'route' => 'fos_user_security_logout'] );

        return $menu;
    }

    public function createMainMenu( array $options )
    {
        $menu = $this->factory->createItem( 'home' );
        $menu->setChildrenAttribute( 'class', 'nav navbar-nav' );

        $menu->addChild( 'home', ['label' => 'common.home', 'route' => 'root'] )
                ->setAttribute( 'icon', 'fa fa-home' );
        if( !$this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $menu->addChild( 'login', ['label' => 'common.log_in', 'route' => 'fos_user_security_login'] )
                    ->setAttribute( 'icon', 'fa fa-login' );
        }
        return $menu;
    }

    public function createCalendarMenu( array $options )
    {
        $menu = $this->factory->createItem( 'calendar' );

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $menu->setChildrenAttribute( 'class', 'nav navbar-nav' );
            $menu->addChild( 'calendar', ['label' => 'common.calendar', 'route' => 'calendar'] )
                    ->setAttribute( 'icon', 'fa fa-calendar' );
        }
        return $menu;
    }

    public function createUserMenu( array $options )
    {
        $menu = $this->factory->createItem( 'user' );
        $menu->setChildrenAttribute( 'class', 'nav navbar-nav navbar-right' );

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get( 'security.token_storage' )->getToken()->getUser();
            $username = $user->getUsername();
            $menu->addChild( 'user' )
                    ->setExtra( 'translation_domain', 'AppBundle' )
                    ->setAttribute( 'dropdown', true )
                    ->setAttribute( 'icon', 'fa fa-user' );
            $menu['user']->setLabel( $username )->setExtra( 'translation_domain', false );

            if( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' ) )
            {
                $menu['user']->addChild( 'admin', array('label' => 'common.admin', 'route' => 'app_admin_index') )
                        ->setAttribute( 'icon', 'fa fa-star-o' );
            }
            $menu['user']->addChild( 'edit_profile', array('label' => 'user.profile', 'route' => 'fos_user_profile_edit') )
                    ->setAttribute( 'icon', 'fa fa-edit' );
            $menu['user']->addChild( 'logout', ['label' => 'common.log_out', 'route' => 'fos_user_security_logout'] );
        }

        return $menu;
    }

}
