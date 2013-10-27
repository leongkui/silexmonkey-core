<?php

namespace SilexMonkey\Provider;

use Gigablah\Silex\OAuth\Security\Authentication\Token\OAuthTokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Silex\Application;

/**
 * OAuth user provider interface.
 */
interface OAuthUserProviderInterface extends UserProviderInterface
{
    /**
     * Loads a user based on OAuth credentials.
     *
     * @param OAuthTokenInterface $token
     *
     * @return UserInterface|null
     */
    public function loadUserByOAuthCredentials(Application $app, OAuthTokenInterface $token);
}
