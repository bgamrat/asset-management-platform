<?php

namespace AppBundle\DataFixtures\ORM\Demo;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use AppBundle\Entity\Asset\Brand;
use AppBundle\Entity\Asset\Manufacturer;
use AppBundle\Entity\Asset\Model;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Common\Email;
use AppBundle\Entity\Common\Phone;
use AppBundle\Entity\Common\Address;

class LoadManufacturerData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $categories = $manager->getRepository( 'AppBundle\Entity\Asset\Category' )->findAll();
        if( empty( $categories ) )
        {
            throw CommonException( "There are no category types defined (load them before running this)" );
        }

        $acme = new Manufacturer();
        $acme->setName( 'Acme' );

        $contact = new Person();
        $contact->setFirstname( 'Carmen' );
        $contact->setLastname( 'Red' );
        $contact->setType( $manager->getRepository( 'AppBundle\Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $email = new Email();
        $email->setEmail( 'carmen@acme.example.com' );
        $email->setType( $manager->getRepository( 'AppBundle\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'AppBundle\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '200 Marginal Way' );
        $address->setCity( 'Portland' );
        $address->setStateProvince( 'ME' );
        $address->setPostalCode( '04103' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $acme->addContact( $contact );
        $manager->persist( $contact );

        $contact = new Person();
        $contact->setFirstname( 'Azure' );
        $contact->setLastname( 'Blue' );
        $contact->setType( $manager->getRepository( 'AppBundle\Entity\Common\PersonType' )->findOneByType( 'sales' ) );
        $email = new Email();
        $email->setEmail( 'azure@acme.example.com' );
        $email->setType( $manager->getRepository( 'AppBundle\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'AppBundle\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '300 Bayview Drive' );
        $address->setCity( 'San Francisco' );
        $address->setStateProvince( 'CA' );
        $address->setPostalCode( '98033' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $acme->addContact( $contact );
        $manager->persist( $contact );

        $rocket = new Brand();
        $rocket->setName( 'Rocket' );
        $acme->addBrand( $rocket );

        $gadget = new Model();
        $gadget->setCategory( $categories[rand( 0, count( $categories ) - 1 )] );
        $gadget->setName( 'Gadget' );
        $gadget->setContainer( false );
        $gadget->setCarnetValue( 5000 );
        $gadget->setDefaultContractValue( 1000 );
        $gadget->setDefaultEventValue( 200 );
        $rocket->addModel( $gadget );
        $manager->persist( $gadget );

        $wingding = new Model();
        $wingding->setCategory( $categories[rand( 0, count( $categories ) - 1 )] );
        $wingding->setName( 'Wingding' );
        $wingding->setContainer( false );
        $wingding->setCarnetValue( 15000 );
        $wingding->setDefaultContractValue( 1000 );
        $wingding->setDefaultEventValue( 600 );
        $rocket->addModel( $wingding );

        $foo = new Model();
        $foo->setCategory( $categories[rand( 0, count( $categories ) - 1 )] );
        $foo->setName( 'Foo' );
        $foo->setContainer( false );
        $foo->setCarnetValue( 105000 );
        $foo->setDefaultContractValue( 21000 );
        $foo->setDefaultEventValue( 1600 );
        $rocket->addModel( $foo );

        $bar = new Model();
        $bar->setCategory( $categories[rand( 0, count( $categories ) - 1 )] );
        $bar->setName( 'Bar' );
        $bar->setContainer( false );
        $bar->setCarnetValue( 75000 );
        $bar->setDefaultContractValue( 2100 );
        $bar->setDefaultEventValue( 160 );
        $rocket->addModel( $bar );

        $baz = new Model();
        $baz->setCategory( $categories[rand( 0, count( $categories ) - 1 )] );
        $baz->setName( 'Baz' );
        $baz->setContainer( false );
        $baz->setCarnetValue( 10500 );
        $baz->setDefaultContractValue( 210 );
        $baz->setDefaultEventValue( 160 );
        $rocket->addModel( $baz );

        $baz->addExtend( $foo );
        $bar->addExtend( $foo );
        $wingding->addRequire( $foo );
        $gadget->addRequire( $baz );

        $manager->persist( $baz );
        $manager->persist( $bar );
        $manager->persist( $foo );
        $manager->persist( $wingding );

        $manager->persist( $rocket );

        $shazam = new Brand();
        $shazam->setName( 'Shazam' );
        $acme->addBrand( $shazam );
        $manager->persist( $shazam );

        $manager->persist( $acme );
        $manager->flush();
    }

}
