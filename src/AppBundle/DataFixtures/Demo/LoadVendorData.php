<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\Vendor;
use AppBundle\Entity\Common\Address;
use AppBundle\Entity\Common\Person;

class LoadVendorData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $td = new Vendor();
        $td->setName( 'Zee Cellar' );
        $td->setActive( true );

        $contact = new Person();
        $contact->setFirstname( 'Lavendar' );
        $contact->setLastname( 'Violet' );
        $contact->setType( $manager->getRepository( 'AppBundle\Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $address = new Address();
        $address->setType( $manager->getRepository( 'AppBundle\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '100 Commonwealth Ave' );
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
        return 300;
    }

}