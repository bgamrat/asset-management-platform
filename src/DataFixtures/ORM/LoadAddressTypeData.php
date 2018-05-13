<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Common\AddressType;

class LoadAddressTypeData extends Fixture
{

    public function load( ObjectManager $manager )
    {

        $choices = [ 'home','home-alternate','office','receiving','sales','service','support','venue'];
        foreach( $choices as $c )
        {
            $addressType = new AddressType();
            $addressType->setType( $c );
            $manager->persist( $addressType );
        }

        $manager->flush();
    }

}
