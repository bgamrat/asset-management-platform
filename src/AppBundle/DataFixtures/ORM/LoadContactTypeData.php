<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Common\ContactType;

class LoadContactTypeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $choices = [ "carrier", "client", "manufacturer", "other", "vendor", "venue"];
        foreach( $choices as $c )
        {
            $contactType = new ContactType();
            $contactType->setEntity( $c );
            $contactType->setActive( true );
            $manager->persist( $contactType );
        }

        $manager->flush();
    }

}
