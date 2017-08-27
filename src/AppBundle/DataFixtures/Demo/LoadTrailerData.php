<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use AppBundle\Entity\Asset\Trailer;

class LoadTrailerData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $locations = $manager->getRepository( 'AppBundle\Entity\Asset\Location' )->findAll();
        if( empty( $locations ) )
        {
            throw new CommonException( "There are no locations defined (load them before running this)" );
        }
        $location = $locations[rand( 0, count( $locations ) - 1 )];
        $trailer = new Trailer();
        $trailer->setName( 'Main' );
        $trailer->setCost( 19000000 );
        $trailer->setDescription( 'Big trailer of stuff' );
        $trailer->setLocation( $location );
        $trailer->setModel( $manager->getRepository( 'AppBundle\Entity\Asset\Model' )->findOneByName( 'Main-Box' ) );

        $box = new Trailer();
        $box->setName( 'Box' );
        $box->setCost( 5200000 );
        $box->setActive( true );
        $box->setLocation( $location );
        $box->setModel( $manager->getRepository( 'AppBundle\Entity\Asset\Model' )->findOneByName( 'Box' ) );
        $manager->persist( $box );

        $trailer->addRequire( $box );
        $trailer->setActive( true );
        $manager->persist( $trailer );
        $manager->flush();
    }

    public function getOrder()
    {
        return 1010;
    }

}
