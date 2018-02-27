<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\IssueType;

class LoadIssueTypeData implements FixtureInterface
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
