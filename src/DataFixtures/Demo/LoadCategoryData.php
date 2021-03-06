<?php

Namespace App\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $top = $manager->getRepository( 'App\Entity\Asset\Category' )->findOneByName( 'top' );

        $trailer = new Category();
        $trailer->setName( 'trailer' )->setInUse( true )->setPosition( 0 )->setParent( $top )->setFullName();
        $manager->persist( $trailer );

        $bundle = new Category();
        $bundle->setName( 'bundle' )->setInUse( true )->setPosition( 1 )->setParent( $top )->setFullName();
        $manager->persist( $bundle );

        $set = new Category();
        $set->setName( 'set' )->setInUse( true )->setPosition( 2 )->setParent( $top )->setFullName();
        $manager->persist( $set );

        $thingies = new Category();
        $thingies->setName( 'thingies' )->setInUse( true )->setPosition( 3 )->setParent( $top )->setFullName();
        $manager->persist( $thingies );

        $widget = new Category();
        $widget->setName( 'widget' )->setInUse( true )->setPosition( 0 )->setParent( $thingies )->setFullName();
        $manager->persist( $widget );

        $gazinta = new Category();
        $gazinta->setName( 'gazinta' )->setInUse( true )->setPosition( 0 )->setParent( $widget )->setFullName();
        $manager->persist( $gazinta );

        $gazada = new Category();
        $gazada->setName( 'gazada' )->setInUse( true )->setPosition( 1 )->setParent( $widget )->setFullName();
        $manager->persist( $gazada );

        $doodad = new Category();
        $doodad->setName( 'doodad' )->setInUse( true )->setPosition( 1 )->setParent( $thingies )->setFullName();
        $manager->persist( $doodad );

        $whatchamacallit = new Category();
        $whatchamacallit->setName( 'whatchamacallit' )->setInUse( true )->setPosition( 2 )->setParent( $thingies )->setFullName();
        $manager->persist( $whatchamacallit );

        $extender = new Category();
        $extender->setName( 'extender' )->setInUse( true )->setPosition( 3 )->setParent( $thingies )->setFullName();
        $manager->persist( $extender );

        $option = new Category();
        $option->setName( 'option' )->setInUse( true )->setPosition( 0 )->setParent( $extender )->setFullName();
        $manager->persist( $option );

        $addon = new Category();
        $addon->setName( 'addon' )->setInUse( true )->setPosition( 1 )->setParent( $extender )->setFullName();
        $manager->persist( $addon );

        $manager->flush();
    }

    public function getOrder()
    {
        return 100;
    }

}
