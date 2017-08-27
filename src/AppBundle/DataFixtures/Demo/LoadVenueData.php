<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Venue\Venue;
use AppBundle\Entity\Common\Address;

class LoadVenueData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $td = new Venue();
        $td->setName( 'TD Garden' );
        $address = new Address();
        $address->setType( $manager->getRepository( 'AppBundle\Entity\Common\AddressType' )->findOneByType( 'venue' ) );
        $address->setStreet1( '100 Legends Way' );
        $address->setCity( 'Boston' );
        $address->setStateProvince( 'MA' );
        $address->setPostalCode( '02114' );
        $td->setAddress( $address );
        $td->setActive( true );
        $manager->persist( $td );
        $manager->flush();
    }

    public function getOrder()
    {
        return 300;
    }

}
