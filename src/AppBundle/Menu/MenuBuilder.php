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
        $menu = $this->factory->createItem( 'root', [ 'label' => 'common.admin'] )->setExtra( 'translation_domain', $this->container->get( 'translator' )->getLocale() );

        $menu->addChild( 'admin', ['route' => 'root', 'label' => 'common.home'] );
        $menu['admin']->addChild( 'admin-assets', ['label' => 'common.assets'] );
        $menu['admin']['admin-assets']->addChild( 'equipment', ['label' => 'asset.equipment', 'route' => 'app_admin_asset_equipment_index'] );
        $menu['admin']['admin-assets']->addChild( 'issues', ['label' => 'common.issues', 'route' => 'app_admin_asset_issue_index'] );
        $menu['admin']['admin-assets']->addChild( 'manufacturers', ['label' => 'asset.manufacturers', 'route' => 'app_admin_asset_manufacturer_index'] );
        $menu['admin']['admin-assets']->addChild( 'trailers', ['label' => 'asset.trailers', 'route' => 'app_admin_asset_trailer_index'] );
        $menu['admin']['admin-assets']->addChild( 'vendors', ['label' => 'asset.vendors', 'route' => 'app_admin_asset_vendor_index'] );
        $menu['admin']['admin-assets']->addChild( 'configuration', [ 'label' => 'common.configuration'] );
        $menu['admin']['admin-assets']['configuration']->addChild( 'statuses', ['label' => 'asset.asset-statuses', 'route' => 'app_admin_asset_assetstatus_index'] );
        $menu['admin']['admin-assets']['configuration']->addChild( 'categories', ['label' => 'asset.categories', 'route' => 'app_admin_asset_category_index'] );
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $menu['admin']['admin-assets']['configuration']->addChild( 'location_types', ['label' => 'asset.location_types', 'route' => 'app_admin_asset_locationtype_index'] );
        }

        $menu['admin']->addChild( 'admin-clients', ['label' => 'common.clients', 'route' => 'app_admin_client_client_index'] );
        $menu['admin']['admin-clients']->addChild( 'clients', ['label' => 'common.clients'] );

        $menu['admin']->addChild( 'admin-schedule', ['label' => 'common.schedule', 'route' => 'app_admin_schedule_default_index'] );
        $menu['admin']['admin-schedule']->addChild( 'events', ['label' => 'common.events', 'route' => 'app_admin_schedule_event_index'] );
        $menu['admin']['admin-schedule']->addChild( 'service', ['label' => 'common.service', 'route' => 'app_admin_schedule_service_index'] );
        $menu['admin']['admin-schedule']->addChild( 'shop', ['label' => 'common.shop', 'route' => 'app_admin_schedule_shop_index'] );
        $menu['admin']['admin-schedule']->addChild( 'park', ['label' => 'common.park', 'route' => 'app_admin_schedule_park_index'] );

        $menu['admin']->addChild( 'admin-common', ['label' => 'common.common'] );
        $menu['admin']['admin-common']->addChild( 'people', ['label' => 'common.people', 'route' => 'app_admin_common_person_index'] );

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN_USER' ) )
        {
            $menu['admin']->addChild( 'user', ['label' => 'common.users'] );

            $menu['admin']['user']->addChild( 'users', ['label' => 'common.users', 'route' => 'app_admin_user_default_index'] );
            $menu['admin']['user']->addChild( 'invitations', ['label' => 'user.invitation', 'route' => 'app_admin_user_invitation_index'] );
        }

        // $menu->addChild( 'logout', ['class' => 'right', 'label' => 'common.log_out', 'route' => 'fos_user_security_logout'] );

        return $menu;
    }

    public function createMainMenu( array $options )
    {
        $menu = $this->factory->createItem( 'home' );
        $menu->setChildrenAttribute( 'class', 'nav navbar-nav' );

        $menu->addChild( 'home', ['label' => 'common.home', 'route' => 'root'] )
                ->setAttribute( 'icon', 'fa fa-home' );
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
            $menu->setChildrenAttribute( 'class', 'nav navbar-nav' );
            $menu->addChild( 'map', ['label' => 'common.map', 'route' => 'map'] )
                    ->setAttribute( 'icon', 'fa fa-map' );
        }
        return $menu;
    }

    public function createTrailerMenu( array $options )
    {
        $menu = $this->factory->createItem( 'trailer' );

        $menu->setChildrenAttribute( 'class', 'nav navbar-nav' );
        $menu->addChild( 'trailer', ['label' => 'asset.trailers', 'route' => 'trailers'] )
                ->setAttribute( 'icon', 'fa fa-truck' );

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
                $menu['user']->addChild( 'admin', array('label' => 'common.admin', 'route' => 'app_admin_asset_equipment_index') )
                        ->setAttribute( 'icon', 'fa fa-star-o' );
            }
            $menu['user']->addChild( 'edit_profile', array('label' => 'user.profile', 'route' => 'fos_user_profile_edit') )
                    ->setAttribute( 'icon', 'fa fa-edit' );
            $menu['user']->addChild( 'logout', ['label' => 'common.log_out', 'route' => 'fos_user_security_logout'] );
        }
        else
        {
            $menu->addChild( 'login', ['label' => 'common.log_in', 'route' => 'fos_user_security_login'] )
                    ->setAttribute( 'icon', 'fa fa-login' );
        }

        return $menu;
    }

}
