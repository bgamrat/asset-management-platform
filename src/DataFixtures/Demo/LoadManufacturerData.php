<?php

Namespace App\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use App\Entity\Asset\Brand;
use App\Entity\Asset\Manufacturer;
use App\Entity\Asset\Model;
use App\Entity\Common\Person;
use App\Entity\Common\Email;
use App\Entity\Common\Phone;
use App\Entity\Common\Address;

class LoadManufacturerData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $categories = $manager->getRepository( 'Entity\Asset\Category' )->findAll();
        if( empty( $categories ) )
        {
            throw CommonException( "There are no category types defined (load them before running this)" );
        }
        $categoryCount = count( $categories ) - 1;

        $acme = new Manufacturer();
        $acme->setName( 'Acme' );

        $contact = new Person();
        $contact->setFirstname( 'Carmen' );
        $contact->setLastname( 'Red' );
        $contact->setType( $manager->getRepository( 'Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $email = new Email();
        $email->setEmail( 'carmen@acme.example.com' );
        $email->setType( $manager->getRepository( 'Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'Entity\Common\AddressType' )->findOneByType( 'office' ) );
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
        $contact->setType( $manager->getRepository( 'Entity\Common\PersonType' )->findOneByType( 'sales' ) );
        $email = new Email();
        $email->setEmail( 'azure@acme.example.com' );
        $email->setType( $manager->getRepository( 'Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'Entity\Common\AddressType' )->findOneByType( 'office' ) );
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
        $gadget->setCategory( $categories[rand( 0, $categoryCount )] );
        $gadget->setName( 'Gadget' );
        $gadget->setContainer( false );
        $gadget->setCarnetValue( 5000 );
        $gadget->setDefaultContractValue( 1000 );
        $gadget->setDefaultEventValue( 200 );
        $channels = new \stdClass();
        $channels->channels = 6;
        $resolution = new \stdClass();
        $resolution->resolution = 'HD';
        $gadget->setCustomAttributes( [$channels, $resolution] );
        $rocket->addModel( $gadget );
        $manager->persist( $gadget );

        $wingding = new Model();
        $wingding->setCategory( $categories[rand( 0, $categoryCount )] );
        $wingding->setName( 'Wingding' );
        $wingding->setContainer( false );
        $wingding->setCarnetValue( 15000 );
        $wingding->setDefaultContractValue( 1000 );
        $wingding->setDefaultEventValue( 600 );
        $expiration = new \stdClass();
        $expiration->expiration = null;
        $channels = new \stdClass();
        $channels->channels = 12;
        $resolution = new \stdClass();
        $resolution->resolution = '4K';
        $wingding->setCustomAttributes( [$expiration, $channels, $resolution] );
        $rocket->addModel( $wingding );

        $foo = new Model();
        $foo->setCategory( $categories[rand( 0, $categoryCount )] );
        $foo->setName( 'Foo' );
        $foo->setContainer( false );
        $foo->setCarnetValue( 105000 );
        $foo->setDefaultContractValue( 21000 );
        $foo->setDefaultEventValue( 1600 );
        $expiration = new \stdClass();
        $expiration->expiration = null;
        $channels = new \stdClass();
        $channels->channels = 4;
        $foo->setCustomAttributes( [$expiration, $channels] );
        $rocket->addModel( $foo );

        $bar = new Model();
        $bar->setCategory( $categories[rand( 0, $categoryCount )] );
        $bar->setName( 'Bar' );
        $bar->setContainer( false );
        $bar->setCarnetValue( 75000 );
        $bar->setDefaultContractValue( 2100 );
        $bar->setDefaultEventValue( 160 );
        $rocket->addModel( $bar );

        $baz = new Model();
        $baz->setCategory( $categories[rand( 0, $categoryCount )] );
        $baz->setName( 'Baz' );
        $baz->setContainer( false );
        $baz->setCarnetValue( 10500 );
        $baz->setDefaultContractValue( 210 );
        $baz->setDefaultEventValue( 160 );
        $rocket->addModel( $baz );

        $baz->addExtend( $foo );
        $baz->addSatisfies( $categories[rand( 0, $categoryCount )] );
        $baz->addSatisfies( $categories[rand( 0, $categoryCount )] );
        $bar->addExtend( $foo );
        $bar->addSatisfies( $categories[rand( 0, $categoryCount )] );
        $wingding->addSatisfies( $categories[rand( 0, $categoryCount )] );
        $gadget->addSatisfies( $categories[rand( 0, $categoryCount )] );

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

        $highPoint = new Manufacturer();
        $highPoint->setName( 'High Point' );

        $zoomer = new Brand();
        $zoomer->setName( 'Zoomer' );
        $highPoint->addBrand( $zoomer );

        $gadget = new Model();
        $gadget->setCategory( $categories[rand( 0, $categoryCount )] );
        $gadget->setName( 'Gadget' );
        $gadget->setContainer( false );
        $gadget->setCarnetValue( 5000 );
        $gadget->setDefaultContractValue( 1000 );
        $gadget->setDefaultEventValue( 200 );
        $zoomer->addModel( $gadget );
        $manager->persist( $gadget );

        $wingding = new Model();
        $wingding->setCategory( $categories[rand( 0, $categoryCount )] );
        $wingding->setName( 'Wingding' );
        $wingding->setContainer( false );
        $wingding->setCarnetValue( 15000 );
        $wingding->setDefaultContractValue( 1000 );
        $wingding->setDefaultEventValue( 600 );
        $zoomer->addModel( $wingding );
        $manager->persist( $highPoint );

        $boxesWithWheels = new Manufacturer();
        $boxesWithWheels->setName( 'Boxes With Wheels' );
        $boxes = new Brand();
        $boxes->setName( 'Boxes' );
        $boxesWithWheels->addBrand( $boxes );

        $box = new Model();
        $box->setCategory( $manager->getRepository( 'Entity\Asset\Category' )->findOneByName( 'trailer' ) );
        $box->setName( 'Box' );
        $box->setContainer( false );
        $box->setCarnetValue( 4400000 );
        $box->setDefaultContractValue( 21000 );
        $box->setDefaultEventValue( 1600 );
        $boxes->addModel( $box );

        $box = new Model();
        $box->setCategory( $manager->getRepository( 'Entity\Asset\Category' )->findOneByName( 'trailer' ) );
        $box->setName( 'Main-Box' );
        $box->setContainer( false );
        $box->setCarnetValue( 7500000 );
        $box->setDefaultContractValue( 410000 );
        $box->setDefaultEventValue( 16000 );
        $boxes->addModel( $box );

        $manager->persist( $boxes );
        $manager->persist( $boxesWithWheels );

        $manager->flush();
    }

    public function getOrder()
    {
        return 200;
    }

}
