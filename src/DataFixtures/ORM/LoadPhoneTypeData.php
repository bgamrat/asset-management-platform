<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Common\PhoneType;

class LoadPhoneTypeData extends Fixture
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
