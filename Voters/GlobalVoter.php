<?php

namespace Librinfo\SecurityBundle\Voters;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class GlobalVoter
 *
 * Acts as if SONATA_ADMIN_ROLE == ROLE_USER to let us defining
 * our custom rôles hierarchy
 */
class GlobalVoter extends RoleVoter
{
    const ROLE = 'ROLE_SONATA_ADMIN';

    public function supportsAttribute($attribute)
    {
        return $attribute == self::ROLE;
    }

    public function supportsClass($class)
    {
        return true; // supports all classes
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        /** @var RoleInterface[] $roles */
        $roles = $this->extractRoles($token);

        foreach ($roles as $role)
            if ($role->getRole() == 'ROLE_USER' && $attributes[0] == self::ROLE)
                return VoterInterface::ACCESS_GRANTED;

        return parent::vote($token, $object, $attributes);
    }
}