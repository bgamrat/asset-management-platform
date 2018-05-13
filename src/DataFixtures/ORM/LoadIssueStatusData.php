<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\IssueStatus;

class LoadIssueStatusData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $statuses = ['New', 'Broken', 'Check', 'Fixed', 'Closed'];
        foreach( $statuses as $i => $s )
        {
            $issueStatus = new IssueStatus();
            $issueStatus->setOrder( $i + 1 );
            $issueStatus->setStatus( $s );
            $issueStatus->setInUse( true );
            $issueStatus->setDefault( $s === 'New' );
            $manager->persist( $issueStatus );
        }

        $manager->flush();

        $entity = 'App\Entity\Asset\IssueStatus';

        $er = $manager->getRepository( $entity );
        $issueStatuses = $er->findAll();

        foreach( $issueStatuses as $is )
        {
            switch( $is->getName() )
            {
                case 'New':
                    $is->addNext( $er->findOneByStatus( 'Check' ) );
                    break;
                case 'Broken':
                    $is->addNext( $er->findOneByStatus( 'New' ) );
                    $is->addNext( $er->findOneByStatus( 'Check' ) );
                    $is->addNext( $er->findOneByStatus( 'Fixed' ) );
                    break;
                case 'Check':
                    $is->addNext( $er->findOneByStatus( 'New' ) );
                    $is->addNext( $er->findOneByStatus( 'Broken' ) );
                    break;
                case 'Fixed':
                    $is->addNext( $er->findOneByStatus( 'New' ) );
                    $is->addNext( $er->findOneByStatus( 'Closed' ) );
                    break;
                case 'Closed':
                    $is->addNext( $er->findOneByStatus( 'New' ) );
                    break;
            }
            $manager->persist( $is );
        }
        $manager->flush();
    }

}
