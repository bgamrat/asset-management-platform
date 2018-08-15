<?php

Namespace App\DataFixtures\TV;

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

        $categoryRepository = $manager->getRepository( 'App\Entity\Asset\Category' );

        $sonyManufacturer = new Manufacturer();
        $sonyManufacturer->setName( 'Sony' );

        $contact = new Person();
        $contact->setFirstname( 'Carmen' );
        $contact->setLastname( 'Red' );
        $contact->setType( $manager->getRepository( 'App\Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $email = new Email();
        $email->setEmail( 'carmen@sony.example.com' );
        $email->setType( $manager->getRepository( 'App\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'App\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '200 Marginal Way' );
        $address->setCity( 'Portland' );
        $address->setStateProvince( 'ME' );
        $address->setPostalCode( '04103' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $sonyManufacturer->addContact( $contact );
        $manager->persist( $contact );

        $contact = new Person();
        $contact->setFirstname( 'Azure' );
        $contact->setLastname( 'Blue' );
        $contact->setType( $manager->getRepository( 'App\Entity\Common\PersonType' )->findOneByType( 'sales' ) );
        $email = new Email();
        $email->setEmail( 'azure@sony.example.com' );
        $email->setType( $manager->getRepository( 'App\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'App\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '300 Bayview Drive' );
        $address->setCity( 'San Francisco' );
        $address->setStateProvince( 'CA' );
        $address->setPostalCode( '98033' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $sonyManufacturer->addContact( $contact );
        $manager->persist( $contact );

        $sony = new Brand();
        $sony->setName( 'Sony' );
        $sonyManufacturer->addBrand( $sony );

        $hdcu1000 = new Model();
        $hdcu1000->setCategory( $categoryRepository->findOneByName( 'Camera' ) );
        $hdcu1000->setName( 'HDCU1000' );
        $hdcu1000->setContainer( false );
        $hdcu1000->setCarnetValue( 5000 );
        $hdcu1000->setDefaultContractValue( 1000 );
        $hdcu1000->setDefaultEventValue( 200 );
        $hdcu1000->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $resolution = new \stdClass();
        $resolution->resolution = 'HD';
        $hdcu1000->setCustomAttributes( [$resolution] );
        $sony->addModel( $hdcu1000 );
        $manager->persist( $hdcu1000 );

        $hdcu2000 = clone $hdcu1000;
        $hdcu2000->setName( 'HDCU2000' );
        $hdcu2000->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );

        $hdcu2500 = clone $hdcu2000;
        $hdcu2500->setName( 'HDCU2500' );
        $hdcu2500->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );

        $hdcu3300 = clone $hdcu2500;
        $hdcu3300->setName( 'HDCU3300' );
        $hdcu3300->addSatisfies( $categoryRepository->findOneByName( '3x MO' ) );
        $sony->addModel( $hdcu3300 );
        $manager->persist( $hdcu3300 );

        $bpu4000 = new Model();
        $bpu4000->setCategory( $categoryRepository->findOneByName( 'CCU' ) );
        $bpu4000->setName( 'BPU4000' );
        $bpu4000->setContainer( false );
        $bpu4000->setCarnetValue( 5000 );
        $bpu4000->setDefaultContractValue( 1000 );
        $bpu4000->setDefaultEventValue( 200 );
        $bpu4000->addExtend( $hdcu2000 );
        $bpu4000->addExtend( $hdcu2500 );
        $sony->addModel( $bpu4000 );
        $manager->persist( $bpu4000 );

        $hdcu2000->addExtendedBy( $bpu4000 );
        $manager->persist( $hdcu2000 );

        $hdcu2500->addExtendedBy( $bpu4000 );
        $manager->persist( $hdcu2500 );

        $sony->addModel( $hdcu2000 );
        $sony->addModel( $hdcu2500 );

        $bpu4000hdcu2000 = new Model();
        $bpu4000hdcu2000->setCategory( $categoryRepository->findOneByName( 'Camera' ) );
        $bpu4000hdcu2000->setName( 'BPU4000-HDCU2000' );
        $bpu4000hdcu2000->setContainer( false );
        $bpu4000hdcu2000->setCarnetValue( 5000 );
        $bpu4000hdcu2000->setDefaultContractValue( 1000 );
        $bpu4000hdcu2000->setDefaultEventValue( 200 );
        $bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $bpu4000hdcu2000->addRequire( $bpu4000 );
        $bpu4000hdcu2000->addRequire( $hdcu2000 );
        $sony->addModel( $bpu4000hdcu2000 );
        $manager->persist( $bpu4000hdcu2000 );

        $bpu4000hdcu2500 = new Model();
        $bpu4000hdcu2500->setCategory( $categoryRepository->findOneByName( 'Camera' ) );
        $bpu4000hdcu2500->setName( 'F55-BPU4000-HDCU2500' );
        $bpu4000hdcu2500->setContainer( false );
        $bpu4000hdcu2500->setCarnetValue( 5000 );
        $bpu4000hdcu2500->setDefaultContractValue( 1000 );
        $bpu4000hdcu2500->setDefaultEventValue( 200 );
        $bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $bpu4000hdcu2500->addRequire( $bpu4000 );
        $bpu4000hdcu2500->addRequire( $hdcu2500 );
        $sony->addModel( $bpu4000hdcu2500 );
        $manager->persist( $bpu4000hdcu2500 );

        $hfrcode = new Model();
        $hfrcode->setCategory( $categoryRepository->findOneByName( 'Code' ) );
        $hfrcode->setName( 'HFR Code' );
        $hfrcode->setContainer( false );
        $hfrcode->setCarnetValue( 5000 );
        $hfrcode->setDefaultContractValue( 1000 );
        $hfrcode->setDefaultEventValue( 200 );
        $hfrcode->addExtend( $bpu4000 );
        $sony->addModel( $hfrcode );
        $manager->persist( $hfrcode );

        $bpu4000hfrhdcu2000 = new Model();
        $bpu4000hfrhdcu2000->setCategory( $categoryRepository->findOneByName( 'Camera' ) );
        $bpu4000hfrhdcu2000->setName( 'F55-BPU4000-HFR-HDCU2000' );
        $bpu4000hfrhdcu2000->setContainer( false );
        $bpu4000hfrhdcu2000->setCarnetValue( 5000 );
        $bpu4000hfrhdcu2000->setDefaultContractValue( 1000 );
        $bpu4000hfrhdcu2000->setDefaultEventValue( 200 );
        $bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '3x MO' ) );
        $bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '4x MO' ) );
        $bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '6x MO' ) );
        $bpu4000hfrhdcu2000->addRequire( $hfrcode );
        $bpu4000hfrhdcu2000->addRequire( $bpu4000 );
        $bpu4000hfrhdcu2000->addRequire( $hdcu2000 );
        $sony->addModel( $bpu4000hfrhdcu2000 );
        $manager->persist( $bpu4000hfrhdcu2000 );

        $bpu4000hfrhdcu2500 = new Model();
        $bpu4000hfrhdcu2500->setCategory( $categoryRepository->findOneByName( 'Camera' ) );
        $bpu4000hfrhdcu2500->setName( 'F55-BPU4000-HFR-HDCU2500' );
        $bpu4000hfrhdcu2500->setContainer( false );
        $bpu4000hfrhdcu2500->setCarnetValue( 5000 );
        $bpu4000hfrhdcu2500->setDefaultContractValue( 1000 );
        $bpu4000hfrhdcu2500->setDefaultEventValue( 200 );
        $bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '3x MO' ) );
        $bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '4x MO' ) );
        $bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '6x MO' ) );
        $bpu4000hfrhdcu2000->addRequire( $hfrcode );
        $bpu4000hfrhdcu2500->addRequire( $bpu4000 );
        $bpu4000hfrhdcu2500->addRequire( $hdcu2500 );
        $sony->addModel( $bpu4000hfrhdcu2500 );
        $manager->persist( $bpu4000hfrhdcu2500 );
        $manager->persist( $sony );
        $manager->persist( $sonyManufacturer );

        $boxesWithWheels = new Manufacturer();
        $boxesWithWheels->setName( 'Boxes With Wheels' );
        $boxes = new Brand();
        $boxes->setName( 'Boxes' );
        $boxesWithWheels->addBrand( $boxes );

        $box = new Model();
        $box->setCategory( $categoryRepository->findOneByName( 'trailer' ) );
        $box->setName( 'Box' );
        $box->setContainer( false );
        $box->setCarnetValue( 4400000 );
        $box->setDefaultContractValue( 21000 );
        $box->setDefaultEventValue( 1600 );
        $boxes->addModel( $box );

        $box = new Model();
        $box->setCategory( $manager->getRepository( 'App\Entity\Asset\Category' )->findOneByName( 'trailer' ) );
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
