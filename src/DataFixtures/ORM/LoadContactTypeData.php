<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Common\ContactType;

class LoadContactTypeData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $choices = [ "carrier", "client", "manufacturer", "other", "vendor", "venue"];
        foreach( $choices as $c )
        {
            $contactType = new ContactType();
            $contactType->setEntity( $c );
            $contactType->setInUse( true );
            $manager->persist( $contactType );
        }

        $manager->flush();
    }

}
