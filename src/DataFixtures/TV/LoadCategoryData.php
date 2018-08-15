<?php

Namespace App\DataFixtures\TV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Asset\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $top = $manager->getRepository( 'App\Entity\Asset\Category' )->findOneByName( 'top' );

        $camera = new Category();
        $camera->setName( 'Camera' )->setInUse( true )->setPosition( 0 )->setParent( $top )->setFullName();
        $manager->persist( $camera );

        $hdvideo = new Category();
        $hdvideo->setName( 'HD Video' )->setInUse( true )->setPosition( 0 )->setParent( $camera )->setFullName();
        $manager->persist( $hdvideo );

        $mo2x = new Category();
        $mo2x->setName( '2x MO' )->setInUse( true )->setPosition( 1 )->setParent( $camera )->setFullName();
        $manager->persist( $mo2x );

        $mo3x = new Category();
        $mo3x->setName( '3x MO' )->setInUse( true )->setPosition( 3 )->setParent( $camera )->setFullName();
        $manager->persist( $mo3x );

        $mo4x = new Category();
        $mo4x->setName( '4x MO' )->setInUse( true )->setPosition( 4 )->setParent( $camera )->setFullName();
        $manager->persist( $mo4x );

        $mo6x = new Category();
        $mo6x->setName( '6x MO' )->setInUse( true )->setPosition( 6 )->setParent( $camera )->setFullName();
        $manager->persist( $mo6x );

        $mo8x = new Category();
        $mo8x->setName( '8x MO' )->setInUse( true )->setPosition( 8 )->setParent( $camera )->setFullName();
        $manager->persist( $mo8x );

        $k4video = new Category();
        $k4video->setName( '4K Video' )->setInUse( true )->setPosition( 0 )->setParent( $camera )->setFullName();
        $manager->persist( $k4video );

        $ccu = new Category();
        $ccu->setName( 'CCU' )->setInUse( true )->setPosition( 0 )->setParent( $top )->setFullName();
        $manager->persist( $ccu );

        $bpu = new Category();
        $bpu->setName( 'BPU' )->setInUse( true )->setPosition( 0 )->setParent( $ccu )->setFullName();
        $manager->persist( $bpu );

        $code = new Category();
        $code->setName( 'Code' )->setInUse( true )->setPosition( 0 )->setParent( $bpu )->setFullName();
        $manager->persist( $code );

        $manager->flush();
    }

    public function getOrder()
    {
        return 100;
    }

}
