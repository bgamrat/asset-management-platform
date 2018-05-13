<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff\Role;

class LoadRoleData extends Fixture
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
