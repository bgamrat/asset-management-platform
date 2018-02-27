<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\Category;

class LoadCategoryData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $top = new Category();
        $top->setName( 'top' );
        $top->setInUse( true );
        $top->setPosition( 0 );
        $top->setParent( null );
        $top->setFullName();
        $manager->persist( $top );
        $manager->flush();
    }

}
