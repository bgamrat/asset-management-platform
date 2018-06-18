<?php

Namespace App\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Venue\Venue;
use App\Entity\Common\Address;
use App\Entity\Common\Person;

class LoadVenueData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $td = new Venue();
        $td->setName( 'TD Garden' );
        $address = new Address();
        $address->setType( $manager->getRepository( 'App\Entity\Common\AddressType' )->findOneByType( 'venue' ) );
        $address->setStreet1( '100 Legends Way' );
        $address->setCity( 'Boston' );
        $address->setStateProvince( 'MA' );
        $address->setPostalCode( '02114' );
        $td->setAddress( $address );
        $td->setActive( true );

        $contact = new Person();
        $contact->setFirstname( 'Cerulean' );
        $contact->setLastname( 'Blue' );
        $contact->setType( $manager->getRepository( 'App\Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $address = new Address();
        $address->setType( $manager->getRepository( 'App\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '100 Legends Way' );
        $address->setCity( 'Boston' );
        $address->setStateProvince( 'MA' );
        $address->setPostalCode( '02114' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $td->addContact( $contact );

        $manager->persist( $td );
        $manager->flush();
    }

    public function getOrder()
    {
        return 400;
    }

}
