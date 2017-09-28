<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\LocationType;

class LoadLocationTypeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $inTransitLocation = new LocationType();
        $inTransitLocation->setName( 'In Transit' );
        $inTransitLocation->setEntity( 'other' );
        $inTransitLocation->setActive( true );
        $inTransitLocation->setDefault( false );
        $manager->persist( $inTransitLocation );

        $unknownLocation = new LocationType();
        $unknownLocation->setName( 'Unknown' );
        $unknownLocation->setEntity( 'other' );
        $unknownLocation->setActive( true );
        $unknownLocation->setDefault( false );
        $manager->persist( $unknownLocation );

        $caseLocation = new LocationType();
        $caseLocation->setName( 'Case' );
        $caseLocation->setLocation( 'asset' );
        $caseLocation->setEntity( 'asset' );
        $caseLocation->setUrl( '/api/store/cases' );
        $caseLocation->setActive( true );
        $caseLocation->setDefault( false );
        $manager->persist( $caseLocation );

        $manufacturerLocation = new LocationType();
        $manufacturerLocation->setName( 'Manufacturer' );
        $manufacturerLocation->setLocation( 'manufacturer' );
        $manufacturerLocation->setEntity( 'contact' );
        $manufacturerLocation->setUrl( '/api/store/contacts?manufacturer' );
        $manufacturerLocation->setActive( true );
        $manufacturerLocation->setDefault( false );
        $manager->persist( $manufacturerLocation );

        $shopLocation = new LocationType();
        $shopLocation->setName( 'Shop' );
        $shopLocation->setEntity( 'other' );
        $shopLocation->setUrl( null );
        $shopLocation->setActive( true );
        $shopLocation->setDefault( false );
        $manager->persist( $shopLocation );

        $trailerLocation = new LocationType();
        $trailerLocation->setName( 'Trailer' );
        $trailerLocation->setLocation( 'trailer' );
        $trailerLocation->setEntity( 'trailer' );
        $trailerLocation->setUrl( '/api/store/trailers' );
        $trailerLocation->setActive( true );
        $trailerLocation->setDefault( true );
        $manager->persist( $trailerLocation );

        $vendorLocation = new LocationType();
        $vendorLocation->setName( 'Vendor' );
        $vendorLocation->setLocation( 'vendor' );
        $vendorLocation->setEntity( 'contact' );
        $vendorLocation->setUrl( '/api/store/contacts?vendor' );
        $vendorLocation->setActive( true );
        $vendorLocation->setDefault( false );
        $manager->persist( $vendorLocation );

        $venueLocation = new LocationType();
        $venueLocation->setName( 'Venue' );
        $venueLocation->setLocation( 'venue' );
        $venueLocation->setEntity( 'contact' );
        $venueLocation->setUrl( '/api/store/contacts?venue' );
        $venueLocation->setActive( true );
        $venueLocation->setDefault( false );
        $manager->persist( $venueLocation );

        $manager->flush();
    }

}
