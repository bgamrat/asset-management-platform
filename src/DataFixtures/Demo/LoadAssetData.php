<?php

Namespace App\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\Asset;
use App\Entity\Asset\Barcode;
use App\Entity\CustomAttribute;

class LoadAssetData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $assetStatuses = $manager->getRepository( 'App\Entity\Asset\AssetStatus' )->findAll();
        if( empty( $assetStatuses ) )
        {
            throw CommonException( "There are no asset statuses defined (load them before running this)" );
        }
        // It's no fun if all the stuff is broken - make it so most stuff works
        $operational = 0;
        $assetStatusCount = count( $assetStatuses ) - 1;
        foreach ($assetStatuses as $i => $as) {
            if ($as->getName() === 'Operational') {
                $operational = $i;
                break;
            }
        }

        $models = $manager->getRepository( 'App\Entity\Asset\Model' )->findAll();
        if( empty( $models ) )
        {
            throw CommonException( "There are no model types defined (load them before running this)" );
        }
        $modelCount = count( $models ) - 1;

        $trailerLocationType = $manager->getRepository( 'App\Entity\Asset\LocationType' )->findOneBy( ['entity' => 'trailer'] );

        $locations = $manager->getRepository( 'App\Entity\Asset\Location' )->findByType( $trailerLocationType->getId() );
        if( empty( $locations ) )
        {
            throw new CommonException( "There are no locations defined (load them before running this)" );
        }
        $locationCount = count( $locations ) - 1;
        $durations = ['+1 week', '+1 month', '+2 months', '+3 months', '+4 months', '+6 months', '+1 year'];
        $durationCount = count( $durations ) - 1;

        for( $i = 0; $i < 250; $i++ )
        {
            $location = $locations[rand( 0, $locationCount )];
            $entityData = $manager->getReference( 'App\Entity\Asset\Trailer', $location->getEntity() );
            $location->setEntityData( $entityData );

            $item = new Asset();
            $item->setModel( $models[rand( 0, $modelCount )] );
            $item->setCost( (float) rand( 1000, 150000 ) );
            $item->setLocation( $location );
            $item->setLocationText( $entityData->getName() );
            $numberFormatter = new \NumberFormatter( 'en_US', \NumberFormatter::ORDINAL );
            $n = $numberFormatter->format( $i );
            $item->setComment( 'This is the ' . $n . ' item' );
            $odds = rand(0,10);
            $status = $odds > 8 ? rand( 0, $assetStatusCount ) : $operational;
            $item->setStatus( $assetStatuses[$status] );
            $item->setSerialNumber( preg_replace( '/\D/', '', md5( rand( 0, 10000 ) ) ) );
            $item->setValue( (float) rand( 1000, 30000 ) );
            $item->setPurchased( new \DateTime( '-' . rand( 0, 5 ) . ' years' ) );

            $duration = new \DateTime( $durations[rand( 0, $durationCount )] );
            $expiration = new CustomAttribute();
            $expiration->setKey( 'expiration' )->setValue( $duration->format( 'Y-m-d' ) );
            $channels = new CustomAttribute();
            $channels->setKey( 'channels' )->setValue( rand( 3, 16 ) );
            $item->setCustomAttributes( [ $expiration, $channels] );

            $barcode = new Barcode();
            $barcode->setBarcode( str_pad( (string) rand( 0, 99999 ), 5, '0', STR_PAD_LEFT ) );
            $barcode->setAsset( $item );
            $item->addBarcode( $barcode );
            $manager->persist( $item );
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1100;
    }

}
