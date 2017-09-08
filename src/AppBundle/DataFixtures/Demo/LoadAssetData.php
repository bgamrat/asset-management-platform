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

        $one = new Asset();
        $one->setModel( $models[rand( 0, $modelCount )] );
        $one->setCost( (float) rand( 1000, 50000 ) );

        $location = $locations[rand( 0, $locationCount )];

        $entityData = $manager->getReference( 'AppBundle\Entity\Asset\Trailer', $location->getEntity() );
        $location->setEntityData( $entityData );

        $one->setLocation( $location );
        $one->setLocationText( $location->getEntityData()->getName() );
        $one->setComment( 'This is the first item' );
        $one->setStatus( $assetStatuses[rand( 0, $assetStatusCount )] );
        $one->setSerialNumber( preg_replace( '/\D/', '', md5( rand( 0, 10000 ) ) ) );
        $one->setValue( (float) rand( 1000, 30000 ) );
        $one->setPurchased( new \DateTime( '-2 years' ) );

        $oneYear = new \DateTime( '+1 year' );
        $expiration = new CustomAttribute();
        $expiration->setKey( 'expiration' )->setValue( $oneYear->format( 'Y-m-d' ) );
        $channels = new CustomAttribute();
        $channels->setKey( 'channels' )->setValue( 4 );
        $one->setCustomAttributes( [ $expiration, $channels] );

        $barcode = new Barcode();
        $barcode->setBarcode( str_pad( (string) rand( 0, 99999 ), 5, '0', STR_PAD_LEFT ) );
        $one->addBarcode( $barcode );
        $manager->persist( $one );
        $manager->flush();
    }

    public function getOrder()
    {
        return 1100;
    }

}
