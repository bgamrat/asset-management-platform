<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\AssetStatus;

class LoadAssetStatusData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $statuses = [ 'Operational', 'Partially operational', 'Not operational', 'Missing', 'Lost', 'Disposed'];
        foreach( $statuses as $s )
        {
            $assetStatus = new AssetStatus();
            $assetStatus->setName( $s );
            $assetStatus->setInUse( true );
            $assetStatus->setDefault( $s === 'Operational' );
            $assetStatus->setAvailable( $s === 'Operational' );
            $manager->persist( $assetStatus );
        }

        $manager->flush();
    }

}
