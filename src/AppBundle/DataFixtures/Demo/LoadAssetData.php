<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\Asset;
use AppBundle\Entity\Asset\Barcode;
use AppBundle\Entity\CustomAttribute;

class LoadAssetData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $assetStatuses = $manager->getRepository( 'AppBundle\Entity\Asset\AssetStatus' )->findAll();
        if( empty( $assetStatuses ) )
        {
            throw CommonException( "There are no asset statuses defined (load them before running this)" );
        }
        $assetStatusCount = count( $assetStatuses ) - 1;

        $models = $manager->getRepository( 'AppBundle\Entity\Asset\Model' )->findAll();
        if( empty( $models ) )
        {
            throw CommonException( "There are no model types defined (load them before running this)" );
        }
        $modelCount = count( $models ) - 1;

        $trailerLocationType = $manager->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneBy( ['entity' => 'trailer'] );

        $locations = $manager->getRepository( 'AppBundle\Entity\Asset\Location' )->findByType( $trailerLocationType->getId() );
        if( empty( $locations ) )
        {
            throw new CommonException( "There are no locations defined (load them before running this)" );
        }
        $locationCount = count( $locations ) - 1;
        $durations = ['+1 week', '+1 month', '+2 months', '+3 months', '+4 months', '+6 months', '+1 year'];
        $durationCount = count( $durations ) - 1;

        for( $i = 0; $i < 25; $i++ )
        {
            $location = $locations[rand( 0, $locationCount )];
            $entityData = $manager->getReference( 'AppBundle\Entity\Asset\Trailer', $location->getEntity() );
            $location->setEntityData( $entityData );

            $item = new Asset();
            $item->setModel( $models[rand( 0, $modelCount )] );
            $item->setCost( (float) rand( 1000, 150000 ) );
            $item->setLocation( $location );
            $item->setLocationText( $location->getEntityData()->getName() );
            $numberFormatter = new \NumberFormatter( 'en_US', \NumberFormatter::ORDINAL );
            $n = $numberFormatter->format( $i);
            $item->setComment( 'This is the ' . $n . ' item' );
            $item->setStatus( $assetStatuses[rand( 0, $assetStatusCount )] );
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
