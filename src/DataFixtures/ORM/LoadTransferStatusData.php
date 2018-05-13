<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\TransferStatus;

class LoadTransferStatusData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $transferStatuses = [ 'Canceled', 'Delayed', 'Delivered', 'In Transit', 'Lost', 'None', 'Packed-Bundled', 'Rerouted', 'Unknown', 'Waiting to Ship'];
        foreach( $transferStatuses as $i => $s )
        {
            $transferStatus = new TransferStatus();
            $transferStatus->setName( $s );
            $transferStatus->setInUse( true );
            $transferStatus->setDefault( $s === 'None' );
            $transferStatus->setInTransit( in_array( $s, ['Delayed', 'In Transit', 'Rerouted'] ) );
            $transferStatus->setLocationDestination( in_array( $s, ['Delivered'] ) );
            $transferStatus->setLocationUnknown( in_array( $s, ['Lost', 'Unknown'] ) );
            $manager->persist( $transferStatus );
        }

        $manager->flush();
    }

}
