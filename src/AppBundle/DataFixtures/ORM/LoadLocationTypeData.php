<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\Location;
use AppBundle\Entity\Asset\LocationType;

class LoadLocationTypeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $inTransitLocation = new LocationType();
        $inTransitLocation->setName( 'In Transit' );
        $inTransitLocation->setEntity( 'other' );
        $inTransitLocation->setInUse( true );
        $inTransitLocation->setDefault( false );
        $manager->persist( $inTransitLocation );

        $location = new Location();
        $location->setType( $inTransitLocation );
        $manager->persist( $location );

        $unknownLocation = new LocationType();
        $unknownLocation->setName( 'Unknown' );
        $unknownLocation->setEntity( 'other' );
        $unknownLocation->setInUse( true );
        $unknownLocation->setDefault( false );
        $manager->persist( $unknownLocation );

        $location = new Location();
        $location->setType( $unknownLocation );
        $manager->persist( $location );

        $caseLocation = new LocationType();
        $caseLocation->setName( 'Case' );
        $caseLocation->setLocation( 'asset' );
        $caseLocation->setEntity( 'asset' );
        $caseLocation->setUrl( '/api/store/cases' );
        $caseLocation->setInUse( true );
        $caseLocation->setDefault( false );
        $manager->persist( $caseLocation );

        $manufacturerLocation = new LocationType();
        $manufacturerLocation->setName( 'Manufacturer' );
        $manufacturerLocation->setLocation( 'manufacturer' );
        $manufacturerLocation->setEntity( 'contact' );
        $manufacturerLocation->setUrl( '/api/store/contacts?manufacturer' );
        $manufacturerLocation->setInUse( true );
        $manufacturerLocation->setDefault( false );
        $manager->persist( $manufacturerLocation );

        $shopLocation = new LocationType();
        $shopLocation->setName( 'Shop' );
        $shopLocation->setEntity( 'other' );
        $shopLocation->setUrl( null );
        $shopLocation->setInUse( true );
        $shopLocation->setDefault( false );
        $manager->persist( $shopLocation );

        $location = new Location();
        $location->setType( $shopLocation );
        $manager->persist( $location );

        $trailerLocation = new LocationType();
        $trailerLocation->setName( 'Trailer' );
        $trailerLocation->setLocation( 'trailer' );
        $trailerLocation->setEntity( 'trailer' );
        $trailerLocation->setUrl( '/api/store/trailers' );
        $trailerLocation->setInUse( true );
        $trailerLocation->setDefault( true );
        $manager->persist( $trailerLocation );

        $vendorLocation = new LocationType();
        $vendorLocation->setName( 'Vendor' );
        $vendorLocation->setLocation( 'vendor' );
        $vendorLocation->setEntity( 'contact' );
        $vendorLocation->setUrl( '/api/store/contacts?vendor' );
        $vendorLocation->setInUse( true );
        $vendorLocation->setDefault( false );
        $manager->persist( $vendorLocation );

        $venueLocation = new LocationType();
        $venueLocation->setName( 'Venue' );
        $venueLocation->setLocation( 'venue' );
        $venueLocation->setEntity( 'contact' );
        $venueLocation->setUrl( '/api/store/contacts?venue' );
        $venueLocation->setInUse( true );
        $venueLocation->setDefault( false );
        $manager->persist( $venueLocation );

        $manager->flush();
    }

}
