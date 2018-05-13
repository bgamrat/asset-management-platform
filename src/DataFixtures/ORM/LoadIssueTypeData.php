<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\IssueType;

class LoadIssueTypeData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $types = [ 'Configuration', 'Maintenance', 'Missing', 'Repair'];
        foreach( $types as $i => $s )
        {
            $issueType = new IssueType();
            $issueType->setType( $s );
            $issueType->setInUse( true );
            $issueType->setDefault( $s === 'Repair' );
            $manager->persist( $issueType );
        }

        $manager->flush();
    }

}
