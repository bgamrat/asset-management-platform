<?php

Namespace App\DataFixtures\TV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\Vendor;
use App\Entity\Common\Address;
use App\Entity\Common\Person;

class LoadVendorData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $vendor = new Vendor();
        $vendor->setName( 'Zee Cellar' );
        $vendor->setActive( true );
        $vendor->addBrand( $manager->getRepository( 'App\Entity\Asset\Brand' )->findOneByName( 'Sony' ) );

        $contact = new Person();
        $contact->setFirstname( 'Lavendar' );
        $contact->setLastname( 'Violet' );
        $contact->setType( $manager->getRepository( 'App\Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $address = new Address();
        $address->setType( $manager->getRepository( 'App\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '100 Commonwealth Ave' );
        $address->setCity( 'Boston' );
        $address->setStateProvince( 'MA' );
        $address->setPostalCode( '02114' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $vendor->addContact( $contact );

        $manager->persist( $vendor );
        $manager->flush();
    }

    public function getOrder()
    {
        return 300;
    }

}
