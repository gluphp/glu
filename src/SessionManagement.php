<?php declare(strict_types = 1);

namespace Glu;

use User\LoggedInUser;

final class SessionManagement {
    private static bool $isStarted = false;

    private static ?LoggedInUser $user;

    public static function start() {
        if (self::$isStarted === false) {
            \session_start();
            self::$isStarted = true;
            self::$user = null;
        }
    }

    public static function end() {
        self::start();
        \session_unset();
        \session_destroy();
        self::$isStarted = false;
        self::$user = null;
    }

    public static function userLoggedIn(LoggedInUser $loggedInUser)
    {
        self::start();
        self::$user = $loggedInUser;
        $_SESSION['__logged_in_user'] = $loggedInUser;
        $_SESSION['__logged_in_username'] = $loggedInUser->username;
        $_SESSION['__logged_in_role'] = $loggedInUser->role;
    }

    public static function loggedInRole(): ?string
    {
        self::start();
        return $_SESSION['__logged_in_role'] ?? null;
    }

    public static function retrieveLoggedInUser(): ?LoggedInUser
    {
        self::start();
        return $_SESSION['__logged_in_user'] ?? null;
    }
}
