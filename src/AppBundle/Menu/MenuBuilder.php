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
        $menu->addChild( 'admin', ['label' => 'common.admin'] );
        $menu['admin']->addChild( 'groups', ['label' => 'common.groups'] );
        $menu['admin']->addChild( 'locations', ['label' => 'common.locations'] );
        $menu['admin']->addChild( 'user', ['label' => 'common.users'] );
        $menu['admin']['user']->addChild( 'users', ['label' => 'common.users', 'route' => 'app_admin_user_index'] );
        $menu['admin']['user']->addChild( 'invitations', ['label' => 'user.invitation', 'route' => 'app_admin_user_invitation_index'] );
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

    public function createUserMenu( array $options )
    {

        $menu = $this->factory->createItem( 'user' );
        $menu->setChildrenAttribute( 'class', 'nav navbar-nav navbar-right' );

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get( 'security.token_storage' )->getToken()->getUser();
            $username = $user->getUsername();
            $menu->addChild( 'user', ['label' => $username] )
                    ->setExtra( 'translation_domain', 'AppBundle' )
                    ->setAttribute( 'dropdown', true )
                    ->setAttribute( 'icon', 'fa fa-user' );
            if( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' ) )
            {
                $menu['user']->addChild( 'admin', array('label' => 'common.admin', 'route' => 'app_web_admin_admin_index') )
                        ->setAttribute( 'icon', 'fa fa-star' );
            }
            $menu['user']->addChild( 'edit_profile', array('label' => 'edit.profile', 'route' => 'fos_user_profile_edit') )
                    ->setAttribute( 'icon', 'fa fa-edit' );
            $menu['user']->addChild( 'logout', ['label' => 'common.log_out', 'route' => 'fos_user_security_logout'] );
        }

        return $menu;
    }
}
