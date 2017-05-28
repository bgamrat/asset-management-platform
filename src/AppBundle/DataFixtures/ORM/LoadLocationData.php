<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\LocationType;

class LoadBaseData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $inTransitLocation= new LocationType();
        $inTransitLocation->setName('In Transit');
        $inTransitLocation->setEntity('other');
        $inTransitLocation->setActive(true);
        $inTransitLocation->setDefault(false);
        $manager->persist($inTransitLocation);
                
        $unknownLocation= new LocationType();
        $unknownLocation->setName('Unknown');
        $unknownLocation->setEntity('other');
        $unknownLocation->setActive(true);
        $unknownLocation->setDefault(false);
        $manager->persist($unknownLocation);
        
        $manager->flush();
    }
}