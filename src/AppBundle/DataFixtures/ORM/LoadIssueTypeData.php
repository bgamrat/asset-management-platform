<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\IssueType;

class LoadIssueTypeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $typees = [ 'Configuration', 'Maintenance', 'Missing', 'Repair'];
        foreach( $typees as $i => $s )
        {
            $issueType = new IssueType();
            $issueType->setType( $s );
            $issueType->setActive( true );
            $issueType->setDefault( $s === 'Repair' );
            $manager->persist( $issueType );
        }

        $manager->flush();
    }

}
