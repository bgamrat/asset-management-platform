<?php

namespace AppBundle\Util;

use Doctrine\ORM\EntityManager;

Class Log
{

    protected $history = null;
    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    function init( $history )
    {
        $this->history = $history;
    }

    function translateLocations()
    {
        $locationIds = [];

        if( is_array( $this->history ) )
        {
            foreach( $this->history as $i => $h )
            {
                if( isset( $h['data']['location'] ) && isset( $h['data']['location']['id'] ) )
                {
                    $locationIds[] = $h['data']['location']['id'];
                }
            }
            $repository = $this->em
                    ->getRepository( 'AppBundle:Location' );
            $locations = $repository->findBy( ['id' => $locationIds] );

            $locationNames = [];
            foreach( $locations as $l )
            {
                $locationNames[$l->getId()] = $l->getName();
            }
            foreach( $this->history as $i => $h )
            {
                if( isset( $h['data']['location'] ) && isset( $h['data']['location']['id'] ) )
                {
                    $this->history[$i]['data']['location'] = $locationNames[$h['data']['location']['id']];
                }
            }
        }
        return $this->history;
    }

    function translateModels()
    {
        $modelIds = [];

        if( is_array( $this->history ) )
        {
            foreach( $this->history as $i => $h )
            {
                if( isset( $h['data']['model'] ) && isset( $h['data']['model']['id'] ) )
                {
                    $modelIds[] = $h['data']['model']['id'];
                }
            }
            $repository = $this->em
                    ->getRepository( 'AppBundle:Model' );
            $models = $repository->findBy( ['id' => $modelIds] );

            $modelNames = [];
            foreach( $models as $m )
            {
                $modelNames[$m->getId()] = $m->getBrand()->getName() . ' ' . $m->getName();
            }
            foreach( $this->history as $i => $h )
            {
                if( isset( $h['data']['model'] ) && isset( $h['data']['model']['id'] ) )
                {
                    $this->history[$i]['data']['model'] = $modelNames[$h['data']['model']['id']];
                }
            }
        }
        return $this->history;
    }

    function translateIdsToText()
    {
        $this->translateModels();
        $this->translateLocations();
        return $this->history;
    }

}
