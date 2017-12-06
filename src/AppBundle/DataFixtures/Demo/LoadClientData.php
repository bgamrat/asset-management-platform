<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use AppBundle\Entity\Client\Client;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Common\Email;
use AppBundle\Entity\Common\Address;
use AppBundle\Entity\Client\Contract;
use AppBundle\Entity\Common\CategoryQuantity;
use AppBundle\Entity\Client\Trailer as ClientTrailer;

class LoadClientData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $categories = $manager->getRepository( 'AppBundle\Entity\Asset\Category' )->findAll( false );
        if( empty( $categories ) )
        {
            throw new CommonException( "There are no category types defined (load them before running this)" );
        }
        foreach( $categories as $i => $c )
        {
            if( $c->getName() === 'top' )
            {
                unset( $categories[$i] );
                break;
            }
        }
        $categories = array_values( $categories );
        shuffle( $categories );
        $categoryCount = count( $categories ) - 1;

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
        $manager->persist( $contact );

        $contract = new Contract();
        $contract->setActive( true );
        $contract->setName( 'Benson - 2018' );
        $contract->setStart( new \DateTime( '2018-01-01' ) );
        $contract->setEnd( new \DateTime( '2018-12-31' ) );

        $categoryQuantity = new CategoryQuantity();
        $categoryQuantity->setCategory( array_pop( $categories ) );
        $categoryQuantity->setQuantity( rand( 1, 3 ) );
        $categoryQuantity->setValue( 4000 );
        $contract->addRequiresCategoryQuantity( $categoryQuantity );

        $categoryQuantity = new CategoryQuantity();
        $categoryQuantity->setCategory( array_pop( $categories ) );
        $categoryQuantity->setQuantity( rand( 1, 3 ) );
        $categoryQuantity->setValue( 3000 );
        $contract->addRequiresCategoryQuantity( $categoryQuantity );

        $categoryQuantity = new CategoryQuantity();
        $categoryQuantity->setCategory( array_pop( $categories ) );
        $categoryQuantity->setQuantity( rand( 1, 3 ) );
        $categoryQuantity->setValue( 430 );
        $contract->addRequiresCategoryQuantity( $categoryQuantity );

        $categoryQuantity = new CategoryQuantity();
        $categoryQuantity->setCategory( array_pop( $categories ) );
        $categoryQuantity->setQuantity( rand( 1, 3 ) );
        $categoryQuantity->setValue( 6430 );
        $contract->addAvailableCategoryQuantity( $categoryQuantity );

        $clientTrailer = new ClientTrailer();
        $clientTrailer->setTrailer( $manager->getRepository( 'AppBundle\Entity\Asset\Trailer' )->findOneByName( 'Box' ) );
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
