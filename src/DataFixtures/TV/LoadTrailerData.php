<?php

Namespace App\DataFixtures\TV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use App\Entity\Asset\Trailer;
use App\Entity\Asset\Location;

class LoadTrailerData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $operational = $manager->getRepository( 'App\Entity\Asset\AssetStatus' )->findOneByName( 'Operational' );

        $shop = $manager->getRepository( 'App\Entity\Asset\LocationType' )->findOneByName( 'Shop' );
        $location = $manager->getRepository( 'App\Entity\Asset\Location' )->findOneByType( $shop->getId() );

        if( empty( $location ) )
        {
            throw new CommonException( "There are no locations defined (load them before running this)" );
        }
        $main = new Trailer();
        $main->setName( 'Main' );
        $main->setCost( 19000000 );
        $main->setDescription( 'Big trailer of stuff' );
        $main->setLocation( $location );
        $main->setStatus( $operational );
        $main->setModel( $manager->getRepository( 'App\Entity\Asset\Model' )->findOneByName( 'Main-Box' ) );

        $box = new Trailer();
        $box->setName( 'Box' );
        $box->setCost( 5200000 );
        $box->setActive( true );
        $box->setLocation( $location );
        $box->setStatus( $operational );
        $box->setModel( $manager->getRepository( 'App\Entity\Asset\Model' )->findOneByName( 'Box' ) );
        $manager->persist( $box );

        $main->addRequire( $box );
        $main->setActive( true );
        $manager->persist( $main );

        $trailerLocationType = $manager->getRepository( 'App\Entity\Asset\LocationType' )->findOneByName( 'Trailer' );

        $locationMain = new Location();
        $locationMain->setEntity( $main->getId() );
        $locationMain->setType( $trailerLocationType );
        $manager->persist( $locationMain );

        $locationBox = new Location();
        $locationBox->setEntity( $box->getId() );
        $locationBox->setType( $trailerLocationType );
        $manager->persist( $locationBox );

        $manager->flush();
    }

    public function getOrder()
    {
        return 650;
    }

}
