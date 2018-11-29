<?php

// src/App/Menu/MenuBuilder.php

Namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MenuBuilder {

    private $factory;
    private $security;
    private $tokenStorage;
    private $translator;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $security, TokenStorageInterface $tokenStorage, TranslatorInterface $translator) {
        $this->factory = $factory;
        $this->security = $security;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
    }

    public function createAdminMenu(array $options) {
        $menu = $this->factory->createItem('admin', ['label' => 'common.admin'])
                ->setExtra('translation_domain', $this->translator->getLocale());

        $menu->addChild('admin', ['route' => 'root', 'label' => 'common.home']);

        $menu->addChild('calendar', ['label' => 'common.calendar', 'route' => 'calendar'])
                ->setAttribute('icon', 'fa fa-calendar');
        $menu->addChild('trailer', ['label' => 'asset.trailers', 'route' => 'trailers'])
                ->setAttribute('icon', 'fa fa-truck');

        $menu->addChild('admin-assets', ['label' => 'common.assets', 'route' => 'app_admin_asset_equipment_index'])
                ->setAttribute('dropdown', true);
        $menu['admin-assets']->addChild('carriers', ['label' => 'common.carriers', 'route' => 'app_admin_asset_carrier_index']);
        $menu['admin-assets']->addChild('equipment', ['label' => 'asset.equipment', 'route' => 'app_admin_asset_equipment_index']);
        $menu['admin-assets']->addChild('issues', ['label' => 'common.issues', 'route' => 'app_admin_asset_issue_index']);
        $menu['admin-assets']->addChild('manufacturers', ['label' => 'asset.manufacturers', 'route' => 'app_admin_asset_manufacturer_index']);
        $menu['admin-assets']->addChild('transfer', ['label' => 'asset.transfers', 'route' => 'app_admin_asset_transfer_index']);
        $menu['admin-assets']->addChild('trailers', ['label' => 'asset.trailers', 'route' => 'app_admin_asset_trailer_index']);
        $menu['admin-assets']->addChild('vendors', ['label' => 'asset.vendors', 'route' => 'app_admin_asset_vendor_index']);

        $menu->addChild('admin-clients', ['label' => 'common.clients', 'route' => 'app_admin_client_client_index']);

        $menu->addChild('admin-schedule', ['label' => 'common.schedule', 'route' => 'app_admin_schedule_default_index'])
                ->setAttribute('dropdown', true);
        $menu['admin-schedule']->addChild('events', ['label' => 'common.events', 'route' => 'app_admin_schedule_event_index']);
        $menu['admin-schedule']->addChild('service', ['label' => 'common.service', 'route' => 'app_admin_schedule_service_index']);
        $menu['admin-schedule']->addChild('shop', ['label' => 'common.shop', 'route' => 'app_admin_schedule_shop_index']);
        $menu['admin-schedule']->addChild('park', ['label' => 'common.park', 'route' => 'app_admin_schedule_park_index']);

        $menu->addChild('admin-people', ['label' => 'common.people', 'route' => 'app_admin_common_person_index']);

        $menu->addChild('admin-staff', ['label' => 'common.staff', 'route' => 'app_admin_staff_staff_index']);
        $menu->addChild('admin-venues', ['label' => 'common.venues', 'route' => 'app_admin_venue_venue_index']);

        return $menu;
    }

    public function createMainMenu(array $options) {
        $menu = $this->factory->createItem('home');

        $menu->addChild('home', ['label' => 'common.home', 'route' => 'root'])
                ->setAttribute('icon', 'fa fa-home');
        return $menu;
    }

    public function createCalendarMenu(array $options) {
        $menu = $this->factory->createItem('calendar');

        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild('calendar', ['label' => 'common.calendar', 'route' => 'calendar'])
                    ->setAttribute('icon', 'fa fa-calendar');
            $menu->addChild('map', ['label' => 'common.map', 'route' => 'map'])
                    ->setAttribute('icon', 'fa fa-map');
        }
        return $menu;
    }

    public function createSuperAdminMenu(array $options) {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $menu = $this->factory->createItem('super-admin', ['label' => 'common.admin'])
                    ->setExtra('translation_domain', $this->translator->getLocale());
            $menu->addChild('asset-configuration', ['label' => 'common.assets']);
            $menu['asset-configuration']->setAttribute('dropdown', true);
            $menu['asset-configuration']->addChild('statuses', ['label' => 'asset.asset_statuses', 'route' => 'app_admin_asset_assetstatus_index']);
            $menu['asset-configuration']->addChild('categories', ['label' => 'asset.categories', 'route' => 'app_admin_asset_category_index']);
            $menu['asset-configuration']->addChild('issue_statuses', ['label' => 'asset.issue_statuses', 'route' => 'app_admin_asset_issuestatus_index']);
            $menu['asset-configuration']->addChild('issue_types', ['label' => 'asset.issue_types', 'route' => 'app_admin_asset_issuetype_index']);
            $menu['asset-configuration']->addChild('issue_workflow', ['label' => 'asset.issue_workflow', 'route' => 'app_admin_asset_issuestatus_workflow']);
            $menu['asset-configuration']->addChild('location_types', ['label' => 'asset.location_types', 'route' => 'app_admin_asset_locationtype_index']);
            $menu['asset-configuration']->addChild('sets', ['label' => 'asset.sets', 'route' => 'app_admin_asset_sets_index']);
            $menu['asset-configuration']->addChild('transfer_statuses', ['label' => 'asset.transfer_statuses', 'route' => 'app_admin_asset_transferstatus_index']);

            $menu->addChild('schedule-configuration', ['label' => 'common.schedule']);
            $menu['schedule-configuration']->setAttribute('dropdown', true);
            $menu['schedule-configuration']->addChild('event-role-types', ['label' => 'event.event_role_types', 'route' => 'app_admin_schedule_eventroletype_index']);
            $menu['schedule-configuration']->addChild('time-span-types', ['label' => 'event.time_span_types', 'route' => 'app_admin_schedule_timespantype_index']);

            $menu->addChild('staff-configuration', ['label' => 'common.staff']);
            $menu['staff-configuration']->setAttribute('dropdown', true);
            $menu['staff-configuration']->addChild('employment_statuses', ['label' => 'staff.employment_statuses', 'route' => 'app_admin_staff_employmentstatus_index']);
            $menu['staff-configuration']->addChild('roles', ['label' => 'common.roles', 'route' => 'app_admin_staff_role_index']);

            $menu->addChild('common-configuration', ['label' => 'common.people']);
            $menu['common-configuration']->setAttribute('dropdown', true);
            $menu['common-configuration']->addChild('person-types', ['label' => 'common.person_types', 'route' => 'app_admin_common_persontype_index']);

            $menu->addChild('super-user', ['label' => 'common.users']);
            $menu['super-user']->setAttribute('dropdown', true);
            $menu['super-user']->addChild('users', ['label' => 'common.users', 'route' => 'app_admin_user_default_index']);
            $menu['super-user']->addChild('invitations', ['label' => 'user.invitation', 'route' => 'app_admin_user_invitation_index']);
            $menu['super-user']->addChild('groups', ['label' => 'common.groups', 'route' => 'app_admin_user_group_index']);
        }
        return $menu;
    }

    public function createTrailerMenu(array $options) {
        $menu = $this->factory->createItem('trailer');

        $menu->addChild('trailer', ['label' => 'asset.trailers', 'route' => 'trailers'])
                ->setAttribute('icon', 'fa fa-truck');

        return $menu;
    }

    public function createUserMenu(array $options) {
        $menu = $this->factory->createItem('user');
        $menu->setAttribute('class', 'ml-auto');

        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->tokenStorage->getToken()->getUser();
            $username = $user->getUsername();
            $menu->addChild('user')
                    ->setExtra('translation_domain', 'App')
                    ->setAttribute('dropdown', true)
                    ->setAttribute('icon', 'fa fa-user');
            $menu['user']->setLabel($username)->setExtra('translation_domain', false);
            $menu['user']->addChild('edit_profile', array('label' => 'user.profile', 'route' => 'fos_user_profile_edit'))
                    ->setAttribute('icon', 'fa fa-edit');
            $menu['user']->addChild('logout', ['label' => 'common.log_out', 'route' => 'fos_user_security_logout'])
                    ->setAttribute('icon', 'fa fa-sign-out');
        } else {
            $menu->addChild('login', ['label' => 'common.log_in', 'route' => 'fos_user_security_login'])
                    ->setAttribute('icon', 'fa fa-login');
        }

        return $menu;
    }

}
