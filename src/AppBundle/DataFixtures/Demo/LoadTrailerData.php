<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use AppBundle\Entity\Asset\Trailer;
use AppBundle\Entity\Asset\Location;

class LoadTrailerData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $operational = $manager->getRepository( 'AppBundle\Entity\Asset\AssetStatus' )->findOneByName( 'Operational' );

        $shop = $manager->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneByName('Shop');
        $location = $manager->getRepository('AppBundle\Entity\Asset\Location')->findOneByType($shop->getId());

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
        $main->setModel( $manager->getRepository( 'AppBundle\Entity\Asset\Model' )->findOneByName( 'Main-Box' ) );

        $box = new Trailer();
        $box->setName( 'Box' );
        $box->setCost( 5200000 );
        $box->setActive( true );
        $box->setLocation( $location );
        $box->setStatus( $operational );
        $box->setModel( $manager->getRepository( 'AppBundle\Entity\Asset\Model' )->findOneByName( 'Box' ) );
        $manager->persist( $box );

        $main->addRequire( $box );
        $main->setActive( true );
        $manager->persist( $main );

        $trailerLocationType = $manager->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneByName( 'Trailer' );

        $location = new Location();
        $location->setEntity( $main->getId() );
        $location->setType( $trailerLocationType );
        $manager->persist( $location );

        $location = new Location();
        $location->setEntity( $box->getId() );
        $location->setType( $trailerLocationType );
        $manager->persist( $location );

        $manager->flush();
    }

    public function getOrder()
    {
        return 650;
    }

}
