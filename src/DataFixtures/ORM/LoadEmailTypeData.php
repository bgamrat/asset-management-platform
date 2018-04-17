<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Common\EmailType;

class LoadEmailTypeData implements FixtureInterface
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
