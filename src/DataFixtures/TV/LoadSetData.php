<?php

Namespace App\DataFixtures\TV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\CommonException;
use App\Entity\Asset\Set;

class LoadSetData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $categoryRepository = $manager->getRepository( 'App\Entity\Asset\Category' );
        $modelRepository = $manager->getRepository( 'App\Entity\Asset\Model' );

        $bpu4000hdcu2000 = new Set();
        $bpu4000hdcu2000->addModel( $modelRepository->findOneByName( 'BPU4000' ) );
        $bpu4000hdcu2000->addModel( $modelRepository->findOneByName( 'HDCU2000' ) );
        $bpu4000hdcu2000->setName( 'BPU4000-HDCU2000' );
        $bpu4000hdcu2000->setValue( rand( 600, 1000 ) );
        $bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $manager->persist( $bpu4000hdcu2000 );

        $f55bpu4000hdcu2000 = new Set();
        $f55bpu4000hdcu2000->addModel( $modelRepository->findOneByName( 'BPU4000' ) );
        $f55bpu4000hdcu2000->addModel( $modelRepository->findOneByName( 'HDCU2000' ) );
        $f55bpu4000hdcu2000->setName( 'F55-BPU4000-HDCU2000' );
        $f55bpu4000hdcu2000->setValue( rand( 1000, 1900 ) );
        $f55bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $f55bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $f55bpu4000hdcu2000->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $manager->persist( $f55bpu4000hdcu2000 );

        $f55bpu4000hfrhdcu2000 = new Set();
        $f55bpu4000hfrhdcu2000->addModel( $modelRepository->findOneByName( 'BPU4000' ) );
        $f55bpu4000hfrhdcu2000->addModel( $modelRepository->findOneByName( 'HFR Code' ) );
        $f55bpu4000hfrhdcu2000->addModel( $modelRepository->findOneByName( 'HDCU2000' ) );
        $f55bpu4000hfrhdcu2000->setName( 'F55-BPU4000-HDCU2000' );
        $f55bpu4000hfrhdcu2000->setValue( rand( 1000, 2800 ) );
        $f55bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $f55bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $f55bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $f55bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '3x MO' ) );
        $f55bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '4x MO' ) );
        $f55bpu4000hfrhdcu2000->addSatisfies( $categoryRepository->findOneByName( '6x MO' ) );
        $manager->persist( $f55bpu4000hfrhdcu2000 );

        $bpu4000hdcu2500 = new Set();
        $bpu4000hdcu2500->addModel( $modelRepository->findOneByName( 'BPU4000' ) );
        $bpu4000hdcu2500->addModel( $modelRepository->findOneByName( 'HDCU2500' ) );
        $bpu4000hdcu2500->setName( 'BPU4000-HDCU2500' );
        $bpu4000hdcu2500->setValue( rand( 500, 2000 ) );
        $bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $manager->persist( $bpu4000hdcu2500 );

        $f55bpu4000hdcu2500 = new Set();
        $f55bpu4000hdcu2500->addModel( $modelRepository->findOneByName( 'BPU4000' ) );
        $f55bpu4000hdcu2500->addModel( $modelRepository->findOneByName( 'HDCU2500' ) );
        $f55bpu4000hdcu2500->setName( 'F55-BPU4000-HDCU2500' );
        $f55bpu4000hdcu2500->setValue( rand( 1000, 9000 ) );
        $f55bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $f55bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $f55bpu4000hdcu2500->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $manager->persist( $f55bpu4000hdcu2500 );

        $f55bpu4000hfrhdcu2500 = new Set();
        $f55bpu4000hfrhdcu2500->addModel( $modelRepository->findOneByName( 'BPU4000' ) );
        $f55bpu4000hfrhdcu2500->addModel( $modelRepository->findOneByName( 'HFR Code' ) );
        $f55bpu4000hfrhdcu2500->addModel( $modelRepository->findOneByName( 'HDCU2500' ) );
        $f55bpu4000hfrhdcu2500->setName( 'F55-BPU4000-HDCU2500' );
        $f55bpu4000hfrhdcu2500->setValue( rand( 1000, 9000 ) );
        $f55bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( 'HD Video' ) );
        $f55bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '4K Video' ) );
        $f55bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '2x MO' ) );
        $f55bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '3x MO' ) );
        $f55bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '4x MO' ) );
        $f55bpu4000hfrhdcu2500->addSatisfies( $categoryRepository->findOneByName( '6x MO' ) );

        $manager->persist( $f55bpu4000hfrhdcu2500 );

        $manager->flush();
    }

    public function getOrder()
    {
        return 250;
    }

}
