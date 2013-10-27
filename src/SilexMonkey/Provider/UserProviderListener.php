<?php

namespace SilexMonkey\Provider;

use Gigablah\Silex\OAuth\OAuthEvents;
use Gigablah\Silex\OAuth\Event\GetUserForTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Silex\Application;

/**
 * Listener to match OAuth user with the local user provider.
 */
class UserProviderListener implements EventSubscriberInterface
{
    /**
     * Populate the security token with a user from the local database.
     *
     * @param GetUserForTokenEvent $event
     */
    public function onGetUser(GetUserForTokenEvent $event)
    {
        $userProvider = $event->getUserProvider();

        if (!$userProvider instanceof OAuthUserProvider) {
            return;
        }

        $token = $event->getToken();

        if ($user = $userProvider->loadUserByOAuthCredentials($this->app, $token)) {
            $token->setUser($user);
        }
    }

    public function setApp(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            OAuthEvents::USER => 'onGetUser'
        );
    }
}
