<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Entity\Schedule\TimeSpanType;

class LoadTimeSpanTypeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $choices = [ 'park', 'park&power', 'event', 'additional-event', 'strike', 'maintenance', 'repair'];
        foreach( $choices as $c )
        {
            $timespanType = new TimeSpanType();
            $timespanType->setName( $c );
            $timespanType->setInUse( true );
            $manager->persist( $timespanType );
        }

        $manager->flush();
    }

}
