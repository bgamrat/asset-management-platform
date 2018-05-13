<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Common\EmailType;

class LoadEmailTypeData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $choices = [ 'alternate','emergency','home','office'];
        foreach( $choices as $c )
        {
            $emailType = new EmailType();
            $emailType->setType( $c );
            $manager->persist( $emailType );
        }

        $manager->flush();
    }

}
