<?php

Namespace App\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use Entity\Asset\Location;

class LoadLocationData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $locationType = $manager->getRepository( 'Entity\Asset\LocationType' )->findOneByName( 'Venue' );
        if( empty( $locationType ) )
        {
            throw new CommonException( "The venue location type hasn't been defined (load it before running this)" );
        }

        $venues = $manager->getRepository( 'Entity\Venue\Venue' )->findAll();
        if( empty( $venues ) )
        {
            throw new CommonException( "There are no venues defined (load them before running this)" );
        }
        $venueCount = count( $venues ) - 1;

        $location = new Location();
        $location->setEntity( $venues[rand( 0, $venueCount )]->getId() );
        $location->setType( $locationType );

        $manager->persist( $location );

        $manager->flush();
    }

    public function getOrder()
    {
        return 600;
    }

}
