<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Entity\Common\PhoneType;

class LoadPhoneTypeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $choices = [ 'alternate','emergency','home','mobile','office','on-call','other','security'];
        foreach( $choices as $c )
        {
            $phoneType = new PhoneType();
            $phoneType->setType( $c );
            $manager->persist( $phoneType );
        }

        $manager->flush();
    }

}
