<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Schedule\EventRoleType;

class LoadEventRoleTypeData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $choices = [ 'Crew', 'Driver', 'Engineer', 'Engineer Manager', 'Logisitics',
            'Project Manager', 'Staff', 'Support', 'Technical Lead'];
        foreach( $choices as $c )
        {
            $eventRoleType = new EventRoleType();
            $eventRoleType->setName( $c );
            $eventRoleType->setInUse( true );
            $manager->persist( $eventRoleType );
        }

        $manager->flush();
    }

}
