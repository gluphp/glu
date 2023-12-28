<?php

namespace Glu\Extension\User;

use Glu\DataSource\Source;

class UserManagement
{
    public function login(Source $source, string $username, string $password): ?LoggedInUser
    {
        $user = $source->fetch('SELECT * FROM users WHERE username = :username', [
            'username' => $username
        ]);

        if (password_verify($password, $user['password'])) {
            return new LoggedInUser($username, 'user', ['email' => 'raulfraile@gmail.com']);
        }

        return null;
    }

    public function changePassword(Source $source, string $username, string $newPasswordHashed): bool
    {
        $source->update('users', ['password' => $newPasswordHashed], ['username' => $username]);

        return true;
    }

    public function generatePasswordResetToken(Source $source, string $username, int $expirationSeconds = 3600): string {
        $resetToken = 'xy1234567890abc';

        \file_put_contents(__DIR__ . '/../var/data/tomato/reset_password_' . $username, $resetToken);
    }

    public function verifyPasswordResetToken(Source $source, string $username, string $token): string {
        return $token === \file_get_contents(__DIR__ . '/../var/data/tomato/reset_password_' . $username);
    }
}
