<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Venue\Venue;
use AppBundle\Entity\Common\Address;

class LoadVenueData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $td = new Venue();
        $td->setName( 'TD Garden' );
        $address = new Address();
        $address->setType($manager->getRepository('AppBundle\Entity\Common\AddressType')->findOneByType( 'venue' ) );
        $address->setStreet1( '100 Legends Way' );
        $address->setCity( 'Boston' );
        $address->setStateProvince( 'MA' );
        $address->setPostalCode( '02114' );
        $td->setAddress( $address );
        $td->setActive( true );
        $manager->persist( $td );
        $manager->flush();
    }

}
