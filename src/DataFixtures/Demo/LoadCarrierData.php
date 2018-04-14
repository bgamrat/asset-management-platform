<?php

Namespace App\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Entity\Asset\Carrier;
use Entity\Asset\CarrierService;
use Entity\Common\Person;
use Entity\Common\Phone;

class LoadCarrierData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $contact = new Person();
        $contact->setFirstname( 'Ebony' );
        $contact->setLastname( 'Black' );
        $contact->setType( $manager->getRepository( 'Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $phone = new Phone();
        $phone->setPhone( '1(800)-555-4377' );
        $phone->setType( $manager->getRepository( 'Entity\Common\PhoneType' )->findOneByType( 'office' ) );
        $manager->persist( $phone );
        $contact->addPhone( $phone );
        $manager->persist( $contact );

        $speedyShip = new Carrier();
        $speedyShip->setName( 'SpeedyShip' );

        $speedyShip->setAccountInformation( 'Account number: ' . preg_replace( '/\D/', '', str_shuffle( md5( 'account_number' ) ) ) );
        $speedyShip->addContact( $contact );
        $services = [ 'Standard', 'Tomorrow', 'Next Week'];
        foreach( $services as $s )
        {
            $serviceType = new CarrierService();
            $serviceType->setName( $s );
            $manager->persist( $serviceType );
            $speedyShip->addService( $serviceType );
        }

        $manager->persist( $speedyShip );

        $contact = new Person();
        $contact->setFirstname( 'Snow' );
        $contact->setLastname( 'White' );
        $contact->setType( $manager->getRepository( 'Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $phone = new Phone();
        $phone->setPhone( '1(800)-555-9861' );
        $phone->setType( $manager->getRepository( 'Entity\Common\PhoneType' )->findOneByType( 'office' ) );
        $manager->persist( $phone );
        $contact->addPhone( $phone );
        $manager->persist( $contact );

        $superTruck = new Carrier();
        $superTruck->setName( 'SuperTruck' );
        $superTruck->setAccountInformation( 'Account number: ' . preg_replace( '/\D/', '', str_shuffle( md5( 'account_number' ) ) ) );

        $superTruck->addContact( $contact );
        $services = [ 'Good', 'Better', 'Wicked Good', 'AWESOME'];
        foreach( $services as $s )
        {
            $serviceType = new CarrierService();
            $serviceType->setName( $s );
            $manager->persist( $serviceType );
            $superTruck->addService( $serviceType );
        }

        $manager->persist( $superTruck );
        $manager->flush();
    }

}
