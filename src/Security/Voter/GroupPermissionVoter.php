<?php

namespace App\Security\Voter;

use App\Entity\Group;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;

class GroupPermissionVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        // If the attribute isn't one we support, return false
        if (!in_array($attribute, ['READ', 'WRITE', 'DELETE', 'UPDATE', 'MOVE', 'RESERVE', 'ADD'])) {
            return false;
        }

        // Only vote on Group objects
        if (!$subject instanceof Group) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $group = $subject;

        // Example logic to determine if the user has the permission
        $user = $token->getUser();
        if ($user instanceof User) {
            foreach ($user->getGroups() as $userGroup) {
                if ($userGroup === $group && in_array($attribute, $group->getPermissions())) {
                    return true;
                }
            }
        }

        return false;
    }
}
