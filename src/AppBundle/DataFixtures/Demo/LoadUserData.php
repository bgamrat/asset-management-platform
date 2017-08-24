<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer( ContainerInterface $container = null )
    {
        $this->container = $container;
    }

    public function load( ObjectManager $manager )
    {
        // Thanks to: https://stackoverflow.com/questions/11811102/creating-an-admin-user-using-datafixtures-and-fosuserbundle
        // Get our userManager, you must implement `ContainerAwareInterface`
        $userManager = $this->container->get( 'fos_user.user_manager' );

        // Create our user and set details
        $adminUser = $userManager->createUser();
        $adminUser->setUsername( 'adminuser' );
        $adminUser->setEmail( 'demo@example.com' );
        $adminUser->addRole( 'ROLE_API' );
        $adminUser->addRole( 'ROLE_SUPER_ADMIN' );
        $adminUser->setPlainPassword( '5min128max' );
        $adminUser->setEnabled( true );
        $adminUser->setConfirmationToken( 'whatnot' );

        // Update the user
        $userManager->updateUser( $adminUser, true );
    }

}
