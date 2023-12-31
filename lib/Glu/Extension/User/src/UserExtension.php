<?php

namespace Glu\Extension\User;

use Glu\App;
use Glu\DataSource\Source;
use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\In;
use Glu\Routing\Route;
use Glu\SessionManagement;
use Glu\Templating\Template;
use ParagonIE\ConstantTime\Base64UrlSafe;

final class UserExtension extends BaseExtension
{
    private string $source;
    private ?\Closure $loginHandler;

    public function __construct(
        string $source,
        ?\Closure $loginHandler = null
    ) {
        $this->source = $source;
        $this->loginHandler = $loginHandler ?? function (Source $source, string $username, string $password) {
            return new LoggedInUser('raul', 'user', []);
        };
    }

    public function name(): string
    {
        return 'dev.glu.user';
    }

    public function configuration(): array
    {
        return [
            __DIR__ . '/Template'
        ];
    }

    public function routes(): array
    {
        return [
            new Route('tomato:login', 'GET', '/login', function (In $in) {
                return new Template('login.html.twig');
            }),
            new Route('tomato:login_handle', 'POST', '/login-handle', function (In $in, App $app) {
                $username = $in->request()->form('username');
                $password = $in->request()->form('password');

                $login = \call_user_func($this->loginHandler, $app->source($this->source), $username, $password);

                if ($login !== null) {
                    SessionManagement::userLoggedIn($login);

                    return Response::createRedirect('/');
                }

                $_SESSION['__login_error'] = true;
                $_SESSION['__login_username'] = $username;
                $_SESSION['__login_password'] = $password;

                return Response::createRedirect('/login');
            }),
        new Route('tomato:logout', 'GET', '/logout', function (In $in) {
            SessionManagement::end();

            return Response::createRedirect('/');
        }),

        new Route('tomato:reset_password', 'GET', '/reset-password', function (In $in) {

            return new Template('user/reset_password.html.twig');
        }),

        new Route('tomato:reset_password_handle', 'POST', '/reset-password', function (In $in, App $app) {
            $email = $in->request()->form('username');

            $source = $app->source($this->source);

            $user = $source->fetchOne('SELECT id FROM dp_user WHERE email = :email', ['email' => $email]);
            if (null === $user) {
                return Response::createRedirect('/reset-password-sent');
            }

            $resetToken = Base64UrlSafe::encode(random_bytes(20));

            // guardar el reset token
            $app->source($this->source)->update('dp_user', ['confirmation_token' => $resetToken], ['id' => $user['id']]);
            //\file_put_contents(__DIR__ . '/../var/data/tomato/reset_password_' . $username, $resetToken);
            // enviar email

            return Response::createRedirect('/reset-password-sent');
        }),

        new Route('tomato:reset_password_sent', 'GET', '/reset-password-sent', function (In $in, App $app) {
            return 'If the user exists, a mail was sent';
        }),

        new Route('tomato:reset_password_verify', 'GET', '/reset-password-verify', function (In $in) {
            $username = $in->request()->query('username');
            $resetToken = $in->request()->query('token');

            // comprobar el reset token
            $storedResetToken = \file_get_contents(__DIR__ . '/../var/data/tomato/reset_password_' . $username);

            if ($resetToken === $storedResetToken) {
                return Response::createRedirect('/reset-password-final?username=' . $username . '&token=' . $resetToken);
            }

            return new Response('nooooooooo');
        }),

        new Route('tomato:reset_password_final', 'GET', '/reset-password-final', function (In $in) {
            return new Template('user/reset_password_final.html.twig', [
                'username' => $in->request()->query('username'),
                'token' => $in->request()->query('token')
            ]);
        }),

        new Route('tomato:reset_password_final_handler', 'POST', '/reset-password-final', function (In $in) {
            $username = $in->request()->form('username');
            $resetToken = $in->request()->form('token');

            // comprobar el reset token
            $storedResetToken = \file_get_contents(__DIR__ . '/../var/data/tomato/reset_password_' . $username);

            if ($resetToken !== $storedResetToken) {
                // error, token not valid for user
                return Response::createRedirect('/');
            }

            $passwordHash = password_hash($in->request()->payload['password'], PASSWORD_BCRYPT);
            \call_user_func($this->changePasswordHandler, $this->sources[$this->userManagement['source']], $request->payload['username'], $passwordHash);

            return Response::createRedirect('/login');
        }),

            new Route('tomato:user_area', 'GET', '/user', function (Request $request) {
                return 'hello user';
            })
        ];
    }

}
