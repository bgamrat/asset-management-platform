<?php

namespace AppBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Asset\Category;

class LoadCategoryData implements FixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $top = $manager->getRepository( 'AppBundle\Entity\Asset\Category' )->findOneByName( 'top' );

        $bundle = new Category();
        $bundle->setName( 'bundle' )->setActive( true )->setPosition( 0 )->setParent( $top )->setFullName();
        $manager->persist( $bundle );

        $set = new Category();
        $set->setName( 'set' )->setActive( true )->setPosition( 1 )->setParent( $top )->setFullName();
        $manager->persist( $set );

        $thingies = new Category();
        $thingies->setName( 'thingies' )->setActive( true )->setPosition( 2 )->setParent( $top )->setFullName();
        $manager->persist( $thingies );

        $widget = new Category();
        $widget->setName( 'widget' )->setActive( true )->setPosition( 0 )->setParent( $thingies )->setFullName();
        $manager->persist( $widget );

        $gazinta = new Category();
        $gazinta->setName( 'gazinta' )->setActive( true )->setPosition( 0 )->setParent( $widget )->setFullName();
        $manager->persist( $gazinta );

        $gazada = new Category();
        $gazada->setName( 'gazada' )->setActive( true )->setPosition( 1 )->setParent( $widget )->setFullName();
        $manager->persist( $gazada );

        $doodad = new Category();
        $doodad->setName( 'doodad' )->setActive( true )->setPosition( 1 )->setParent( $thingies )->setFullName();
        $manager->persist( $doodad );

        $whatchamacallit = new Category();
        $whatchamacallit->setName( 'whatchamacallit' )->setActive( true )->setPosition( 2 )->setParent( $thingies )->setFullName();
        $manager->persist( $whatchamacallit );

        $extender = new Category();
        $extender->setName( 'extender' )->setActive( true )->setPosition( 3 )->setParent( $thingies )->setFullName();
        $manager->persist( $extender );

        $option = new Category();
        $option->setName( 'option' )->setActive( true )->setPosition( 0 )->setParent( $extender )->setFullName();
        $manager->persist( $option );

        $addon = new Category();
        $addon->setName( 'addon' )->setActive( true )->setPosition( 1 )->setParent( $extender )->setFullName();
        $manager->persist( $addon );

        $manager->flush();
    }

}
