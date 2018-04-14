<?php

Namespace App\Util;

use Entity\User as UserEntity;
use FOS\UserBundle\Doctrine\GroupManager;

class User
{
    private $groupManager;

    public function __construct( GroupManager $groupManager )
    {
        $this->groupManager = $groupManager;
    }

    public function processGroupUpdates( UserEntity $user, $data )
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
    
    public function processRoleUpdates( UserEntity $user, Array $roles )
    {
        $roleNames = [];
        foreach( $roles as $role )
        {
            $roleNames[] = $role->name;
        }
        $user->setRoles( $roleNames );
    }


}