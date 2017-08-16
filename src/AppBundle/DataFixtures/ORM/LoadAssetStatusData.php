<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\AssetStatus;

class LoadCustomAttributeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $statuses = [ 'Operational', 'Partially operational', 'Not operational', 'Missing', 'Lost', 'Disposed'];
        foreach( $statuses as $s )
        {
            $assetStatus = new AssetStatus();
            $assetStatus->setName( $s );
            $assetStatus->setActive( true );
            $assetStatus->setDefault( $s === 'Operational' );
            $manager->persist( $assetStatus );
        }

        $manager->flush();
    }

}
