<?php

namespace Common\AdminBundle\Util;

use Common\AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\GroupManager;

class Group
{
    private $groupManager;

    public function __construct( GroupManager $groupManager )
    {
        $this->groupManager = $groupManager;
    }

    public function processGroupUpdates( User $user, $data )
    {
        $allGroups = $this->groupManager->findGroups();
        $allGroupNames = [];
        foreach( $allGroups as $g )
        {
            $allGroupNames[] = $g->getName();
        }
        foreach( $allGroupNames as $groupName )
        {
            $g = $this->groupManager->findGroupByName( $groupName );
            if( !in_array( $g->getId(), $data['groups'] ) )
            {
                $user->removeGroup( $g );
            }
            else
            {
                if( !$user->hasGroup( $g ) )
                {
                    $user->addGroup( $g );
                }
            }
        }
    }

}
