<?php

Namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\Category;

class LoadCategoryData extends Fixture
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
