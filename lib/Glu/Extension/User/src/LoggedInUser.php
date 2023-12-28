<?php

namespace Glu\Extension\User\src;

final class LoggedInUser
{
    public function __construct(
        public readonly string $username,
        public readonly string $role,
        public readonly array $additionalInfo,
    )
    {

    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, [$role], true);
    }
}
