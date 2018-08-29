<?php

Namespace App\DataFixtures\TV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use App\Entity\Client\Client;
use App\Entity\Common\Person;
use App\Entity\Common\Email;
use App\Entity\Common\Address;
use App\Entity\Client\Contract;
use App\Entity\Common\CategoryQuantity;
use App\Entity\Client\Trailer as ClientTrailer;

class LoadClientData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $categoryRepository = $manager->getRepository( 'App\Entity\Asset\Category' );
        $categoryCount = $categoryRepository->count( [] );
        if( $categoryCount === 0 )
        {
            throw new CommonException( "There are no categories defined (load them before running this)" );
        }
        $categories = [];
        $categories[] = $categoryRepository->findOneByName( '4K Video' );
        $categories[] = $categoryRepository->findOneByName( '2x MO' );

        $hTv = new Client();
        $hTv->setName( 'Hudson TV' );

        $contact = new Person();
        $contact->setFirstname( 'Viridian' );
        $contact->setLastname( 'Green' );
        $contact->setType( $manager->getRepository( 'App\Entity\Common\PersonType' )->findOneByType( 'representative' ) );
        $email = new Email();
        $email->setEmail( 'viridian@htv.example.com' );
        $email->setType( $manager->getRepository( 'App\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'App\Entity\Common\AddressType' )->findOneByType( 'office' ) );
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
        $contact->setType( $manager->getRepository( 'App\Entity\Common\PersonType' )->findOneByType( 'sales' ) );
        $email = new Email();
        $email->setEmail( 'saffron@htv.example.com' );
        $email->setType( $manager->getRepository( 'App\Entity\Common\EmailType' )->findOneByType( 'office' ) );
        $contact->addEmail( $email );
        $address = new Address();
        $address->setType( $manager->getRepository( 'App\Entity\Common\AddressType' )->findOneByType( 'office' ) );
        $address->setStreet1( '500 Benson Park Lane' );
        $address->setCity( 'Hudson' );
        $address->setStateProvince( 'NH' );
        $address->setPostalCode( '03051' );
        $contact->addAddress( $address );
        $contact->setActive( true );
        $manager->persist( $contact );

        $contract = new Contract();
        $contract->setActive( true );
        $contract->setName( 'Benson - 2018' );
        $contract->setStart( new \DateTime( '2018-01-01' ) );
        $contract->setEnd( new \DateTime( '2018-12-31' ) );

        while( !empty( $categories ) )
        {
            $category = array_pop( $categories );
            if( !empty( $category ) )
            {
                $categoryQuantity = new CategoryQuantity();
                $categoryQuantity->setCategory( $category );
                $categoryQuantity->setQuantity( rand( 1, 3 ) );
                $categoryQuantity->setValue( rand( 4000, 100000 ) );
                $contract->addRequiresCategoryQuantity( $categoryQuantity );
            }
        }

        $clientTrailer = new ClientTrailer();
        $clientTrailer->setTrailer( $manager->getRepository( 'App\Entity\Asset\Trailer' )->findOneByName( 'Box' ) );
        $manager->persist( $clientTrailer );

        $contract->addRequiresTrailers( $clientTrailer );

        $hTv->addContract( $contract );

        $hTv->addContact( $contact );
        $manager->persist( $hTv );

        $catTv = new Client();
        $catTv->setName( 'Cat TV' );
        $manager->persist( $catTv );

        $manager->flush();
    }

    public function getOrder()
    {
        return 1200;
    }

}
