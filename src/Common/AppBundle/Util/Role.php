<?php

namespace Common\AppBundle\Util;

use Common\AppBundle\Form\Admin\User\UserType;
use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\HttpFoundation\Request;

class Role
{
    public function processRoleUpdates( Array $roles )
    {
        $roleNames = [];
        foreach( $roles as $role )
        {
            $roleNames[] = $role->name;
        }
        return $roleNames;
    }

}
