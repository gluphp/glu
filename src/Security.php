<?php

namespace Glu;

use User\LoggedInUser;

final class Security
{
    public function isGranted(LoggedInUser $user, string $role = 'user'): bool
    {
        return \in_array($user->)
    }
}
