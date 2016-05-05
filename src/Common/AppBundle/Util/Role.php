<?php

namespace Common\AppBundle\Util;

use Common\AppBundle\Entity\User;
use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\HttpFoundation\Request;

class Role
{
    public function processRoleUpdates( User $user, Array $roles )
    {
        $roleNames = [];
        foreach( $roles as $role )
        {
            $roleNames[] = $role->name;
        }
        $user->setRoles( $roleNames );
    }

}
