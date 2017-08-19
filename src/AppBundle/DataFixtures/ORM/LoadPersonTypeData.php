<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Common\PersonType;

class LoadPersonTypeData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $choices = [ "client", "employee", "freelance", "representative", "sales", "support", "team", "vendor" ];
        foreach( $choices as $c )
        {
            $personType = new PersonType();
            $personType->setType( $c );
            $manager->persist( $personType );
        }

        $manager->flush();
    }

}
