<?php

namespace AppBundle\DataFixtures\ORM\Demo;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Client\Client;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Common\Email;
use AppBundle\Entity\Common\Phone;
use AppBundle\Entity\Common\Address;

class LoadClientData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $hTv = new Client();
        $hTv->setName( 'Hudson TV' );

        $contact = new Person();
        $contact->setFirstname( 'Viridian' );
        $contact->setLastname( 'Green' );
        $contact->setType( $manager->getRepository( 'AppBundle\Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $email = new Email();
        $email->setEmail( 'viridian@htv.example.com' );
        $email->setType( $manager->getRepository( 'AppBundle\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'AppBundle\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '400 Benson Park Lane' );
        $address->setCity( 'Hudson' );
        $address->setStateProvince( 'NH' );
        $address->setPostalCode( '03051' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $hTv->addContact( $contact );
        $manager->persist( $contact );

        $contact = new Person();
        $contact->setFirstname( 'Saffron' );
        $contact->setLastname( 'Yellow' );
        $contact->setType( $manager->getRepository( 'AppBundle\Entity\Common\PersonType' )->findOneByType( 'sales' ) );
        $email = new Email();
        $email->setEmail( 'saffron@htv.example.com' );
        $email->setType( $manager->getRepository( 'AppBundle\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'AppBundle\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '500 Benson Park Lane' );
        $address->setCity( 'Hudson' );
        $address->setStateProvince( 'NH' );
        $address->setPostalCode( '03051' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $hTv->addContact( $contact );
        $manager->persist( $contact );
        $manager->persist($hTv);

        $catTv = new Client();
        $catTv->setName( 'Cat TV' );
        $manager->persist( $catTv );

        $manager->flush();
    }

}
