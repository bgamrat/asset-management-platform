<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Schedule\TimeSpanType;

class LoadTimeSpanTypeData extends Fixture
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
