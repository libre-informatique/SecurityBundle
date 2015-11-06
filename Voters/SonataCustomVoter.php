<?php

namespace Librinfo\SecurityBundle\Voters;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Security\Handler\RoleSecurityHandler;

class SonataCustomVoter extends RoleSecurityHandler
{
    public function isGranted(AdminInterface $admin, $attributes, $object = null)
    {
        // Put here custom security logic if needed

        return parent::isGranted($admin, $attributes, $object);
    }

}