<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff\EmploymentStatus;

class LoadEmploymentStatusData extends Fixture
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
        foreach( $statuses as $i => $s )
        {
            $status = new EmploymentStatus();
            $status->setName( $s );
            $status->setInUse( true );
            $status->setActive( in_array( $s, ['Full-time', 'Part-time', 'Temporary', 'Intern'] ) );
            $manager->persist( $status );
        }

        $manager->flush();
    }

}
