<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff\Role;

class LoadRoleData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $roles = [ 
            'Driver',
            'Engineer',
            'Field Support',
            'Management',
            'Office',
            'Shop'
            ];
        foreach( $roles as $i => $r )
        {
            $role = new Role();
            $role->setName( $r );
            $role->setInUse( true );
            $manager->persist( $role );
        }

        $manager->flush();
    }

}
