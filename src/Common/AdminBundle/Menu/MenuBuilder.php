<?php

// src/AppBundle/Menu/MenuBuilder.php

namespace Common\AdminBundle\Menu;

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

        $menu->addChild( 'home', ['route' => 'root', 'label' => 'common.home'] )
                ->setExtra( 'translation_domain', 'AppBundle' );
        $menu->addChild( 'admin', ['label' => 'common.admin'] );
        $menu['admin']->addChild( 'groups', ['label' => 'common.groups'] );
        $menu['admin']->addChild( 'locations', ['label' => 'common.locations'] );
        $menu['admin']->addChild( 'user', ['label' => 'common.users'] );
        $menu['admin']['user']->addChild( 'users', ['label' => 'common.users', 'route' => 'common_admin_web_admin_user'] );
        $menu['admin']['user']->addChild( 'invitations', ['label' => 'user.invitation', 'route' => 'common_admin_web_invitation_invitation'] );
        $menu->addChild( 'logout', ['label' => 'common.log_out', 'route' => 'fos_user_security_logout'] );

        return $menu;
    }

}
