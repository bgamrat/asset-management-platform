<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Staff\EmploymentStatus;

class LoadEmploymentStatusData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $statuses = [
            'Full-time',
            'Part-time',
            'Temporary',
            'Intern',
            'Resigned',
            'Retired',
            'Terminated'
            ];
        foreach( $statuses as $i => $r )
        {
            $status = new EmploymentStatus();
            $status->setName( $r );
            $status->setActive( true );
            $manager->persist( $status );
        }

        $manager->flush();
    }

}
