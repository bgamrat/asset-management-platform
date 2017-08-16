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
        $caseLocation->setEntity( 'asset' );
        $caseLocation->setUrl( '/api/store/cases' );
        $caseLocation->setActive( true );
        $caseLocation->setDefault( false );
        $manager->persist( $caseLocation );

        $manufacturerLocation = new LocationType();
        $manufacturerLocation->setName( 'Manufacturer' );
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
        $trailerLocation->setEntity( 'contact' );
        $trailerLocation->setUrl( '/api/store/trailers' );
        $trailerLocation->setActive( true );
        $trailerLocation->setDefault( false );
        $manager->persist( $trailerLocation );

        $vendorLocation = new LocationType();
        $vendorLocation->setName( 'Vendor' );
        $vendorLocation->setEntity( 'contact' );
        $vendorLocation->setUrl( '/api/store/contacts?vendor' );
        $vendorLocation->setActive( true );
        $vendorLocation->setDefault( true );
        $manager->persist( $vendorLocation );

        $venueLocation = new LocationType();
        $venueLocation->setName( 'Venue' );
        $venueLocation->setEntity( 'contact' );
        $venueLocation->setUrl( '/api/store/contacts?venue' );
        $venueLocation->setActive( true );
        $venueLocation->setDefault( false );
        $manager->persist( $venueLocation );

        $manager->flush();
    }

}
