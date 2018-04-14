<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Entity\Schedule\EventRoleType;

class LoadEventRoleTypeData implements FixtureInterface
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
