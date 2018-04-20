<?php

// src/App/Menu/MenuBuilder.php

Namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MenuBuilder
{

    private $factory;
    private $security;
    private $tokenStorage;
    private $translator;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct( FactoryInterface $factory, AuthorizationCheckerInterface $security, TokenStorageInterface $tokenStorage, TranslatorInterface $translator )
    {
        $this->factory = $factory;
        $this->security = $security;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
    }

    public function createAdminMenu( array $options )
    {
        $menu = $this->factory->createItem( 'root', [ 'label' => 'common.admin'] )
                ->setExtra( 'translation_domain', $this->translator->getLocale() );

        $menu->addChild( 'admin', ['route' => 'root', 'label' => 'common.home'] );
        $menu['admin']->addChild( 'admin-assets', ['label' => 'common.assets'] );
        $menu['admin']['admin-assets']->addChild( 'carriers', ['label' => 'common.carriers', 'route' => 'app_admin_asset_carrier_index'] );
        $menu['admin']['admin-assets']->addChild( 'equipment', ['label' => 'asset.equipment', 'route' => 'app_admin_asset_equipment_index'] );
        $menu['admin']['admin-assets']->addChild( 'issues', ['label' => 'common.issues', 'route' => 'app_admin_asset_issue_index'] );
        $menu['admin']['admin-assets']->addChild( 'manufacturers', ['label' => 'asset.manufacturers', 'route' => 'app_admin_asset_manufacturer_index'] );
        $menu['admin']['admin-assets']->addChild( 'transfer', ['label' => 'asset.transfers', 'route' => 'app_admin_asset_transfer_index'] );
        $menu['admin']['admin-assets']->addChild( 'trailers', ['label' => 'asset.trailers', 'route' => 'app_admin_asset_trailer_index'] );
        $menu['admin']['admin-assets']->addChild( 'vendors', ['label' => 'asset.vendors', 'route' => 'app_admin_asset_vendor_index'] );
        $menu['admin']['admin-assets']->addChild( 'asset-configuration', [ 'label' => 'common.configuration'] );
        if( $this->security->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $menu['admin']['admin-assets']['asset-configuration']->addChild( 'statuses', ['label' => 'asset.asset_statuses', 'route' => 'app_admin_asset_assetstatus_index'] );
            $menu['admin']['admin-assets']['asset-configuration']->addChild( 'categories', ['label' => 'asset.categories', 'route' => 'app_admin_asset_category_index'] );
            $menu['admin']['admin-assets']['asset-configuration']->addChild( 'issue_statuses', ['label' => 'asset.issue_statuses', 'route' => 'app_admin_asset_issuestatus_index'] );
            $menu['admin']['admin-assets']['asset-configuration']->addChild( 'issue_types', ['label' => 'asset.issue_types', 'route' => 'app_admin_asset_issuetype_index'] );
            $menu['admin']['admin-assets']['asset-configuration']->addChild( 'issue_workflow', ['label' => 'asset.issue_workflow', 'route' => 'app_admin_asset_issuestatus_workflow'] );
            $menu['admin']['admin-assets']['asset-configuration']->addChild( 'location_types', ['label' => 'asset.location_types', 'route' => 'app_admin_asset_locationtype_index'] );
            $menu['admin']['admin-assets']['asset-configuration']->addChild( 'transfer_statuses', ['label' => 'asset.transfer_statuses', 'route' => 'app_admin_asset_transferstatus_index'] );
        }

        $menu['admin']->addChild( 'admin-clients', ['label' => 'common.clients', 'route' => 'app_admin_client_client_index'] );
        $menu['admin']['admin-clients']->addChild( 'clients', ['label' => 'common.clients'] );

        $menu['admin']->addChild( 'admin-schedule', ['label' => 'common.schedule', 'route' => 'app_admin_schedule_default_index'] );
        $menu['admin']['admin-schedule']->addChild( 'events', ['label' => 'common.events', 'route' => 'app_admin_schedule_event_index'] );
        $menu['admin']['admin-schedule']->addChild( 'service', ['label' => 'common.service', 'route' => 'app_admin_schedule_service_index'] );
        $menu['admin']['admin-schedule']->addChild( 'shop', ['label' => 'common.shop', 'route' => 'app_admin_schedule_shop_index'] );
        $menu['admin']['admin-schedule']->addChild( 'park', ['label' => 'common.park', 'route' => 'app_admin_schedule_park_index'] );
        if( $this->security->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $menu['admin']['admin-schedule']->addChild( 'schedule-configuration', [ 'label' => 'common.configuration'] );
            $menu['admin']['admin-schedule']['schedule-configuration']->addChild( 'event-role-types', ['label' => 'event.event_role_types', 'route' => 'app_admin_schedule_eventroletype_index'] );
            $menu['admin']['admin-schedule']['schedule-configuration']->addChild( 'time-span-types', ['label' => 'event.time_span_types', 'route' => 'app_admin_schedule_timespantype_index'] );
        }

        $menu['admin']->addChild( 'admin-staff', ['label' => 'common.staff'] );
        $menu['admin']['admin-staff']->addChild( 'staff', ['label' => 'common.staff', 'route' => 'app_admin_staff_staff_index'] );
        if( $this->security->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $menu['admin']['admin-staff']->addChild( 'staff-configuration', [ 'label' => 'common.configuration'] );
            $menu['admin']['admin-staff']['staff-configuration']->addChild( 'employment_statuses', ['label' => 'staff.employment_statuses', 'route' => 'app_admin_staff_employmentstatus_index'] );
            $menu['admin']['admin-staff']['staff-configuration']->addChild( 'roles', ['label' => 'common.roles', 'route' => 'app_admin_staff_role_index'] );
        }

        $menu['admin']->addChild( 'admin-common', ['label' => 'common.common'] );
        $menu['admin']['admin-common']->addChild( 'people', ['label' => 'common.people', 'route' => 'app_admin_common_person_index'] );
        if( $this->security->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $menu['admin']['admin-common']->addChild( 'common-configuration', [ 'label' => 'common.configuration'] );
            $menu['admin']['admin-common']['common-configuration']->addChild( 'person-types', ['label' => 'common.person_types', 'route' => 'app_admin_common_persontype_index'] );
        }

        if( $this->security->isGranted( 'ROLE_ADMIN_USER' ) )
        {
            $menu['admin']->addChild( 'user', ['label' => 'common.users'] );

            $menu['admin']['user']->addChild( 'users', ['label' => 'common.users', 'route' => 'app_admin_user_default_index'] );
            $menu['admin']['user']->addChild( 'invitations', ['label' => 'user.invitation', 'route' => 'app_admin_user_invitation_index'] );
            $menu['admin']['user']->addChild( 'groups', ['label' => 'common.groups', 'route' => 'app_admin_user_group_index'] );
        }

        $menu['admin']->addChild( 'admin-venues', ['label' => 'common.venues', 'route' => 'app_admin_venue_venue_index'] );
        $menu['admin']['admin-venues']->addChild( 'venues', ['label' => 'common.venues'] );

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

        if( $this->security->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
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

        if( $this->security->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->tokenStorage->getToken()->getUser();
            $username = $user->getUsername();
            $menu->addChild( 'user' )
                    ->setExtra( 'translation_domain', 'App' )
                    ->setAttribute( 'dropdown', true )
                    ->setAttribute( 'icon', 'fa fa-user' );
            $menu['user']->setLabel( $username )->setExtra( 'translation_domain', false );

            if( $this->security->isGranted( 'ROLE_ADMIN' ) )
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
