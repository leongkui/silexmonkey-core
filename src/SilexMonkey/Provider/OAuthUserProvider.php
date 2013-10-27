<?php

namespace SilexMonkey\Provider;

use SilexMonkey\Models\User as User;
use Gigablah\Silex\OAuth\Security\User\StubUser;
use Gigablah\Silex\OAuth\Security\Authentication\Token\OAuthTokenInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Silex\Application;

class OAuthUserProvider implements UserProviderInterface,OAuthUserProviderInterface
{
    private $users;
    private $credentials;

    /**
     * Constructor.
     *
     * @param array $users       An array of users
     * @param array $credentials A map of usernames with
     */
    public function __construct(array $users = array(), array $credentials = array())
    {
        foreach ($users as $username => $attributes) {
            $password = isset($attributes['password']) ? $attributes['password'] : null;
            $email = isset($attributes['email']) ? $attributes['email'] : null;
            $enabled = isset($attributes['enabled']) ? $attributes['enabled'] : true;
            $roles = isset($attributes['roles']) ? (array) $attributes['roles'] : array();
            $user = new StubUser($username, $password, $email, $roles, $enabled, true, true, true);
            $this->createUser($user);
        }

        $this->credentials = $credentials;
    }

    public function createUser(UserInterface $user)
    {
        if (isset($this->users[strtolower($user->getUsername())])) {
            throw new \LogicException('Another user with the same username already exist.');
        }

        $this->users[strtolower($user->getUsername())] = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        if (isset($this->users[strtolower($username)])) {
            $user = $this->users[strtolower($username)];
        } else {
            $user = new StubUser($username, '', $username . '@example.org', array('ROLE_USER'), true, true, true, true);
            $this->createUser($user);
        }

        return new StubUser($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getRoles(), $user->isEnabled(), $user->isAccountNonExpired(), $user->isCredentialsNonExpired(), $user->isAccountNonLocked());
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthCredentials(Application $app, OAuthTokenInterface $token)
    {
        foreach ($this->credentials as $username => $credentials) {
            foreach ($credentials as $credential) {
                if ($credential['service'] == $token->getService() && $credential['uid'] == $token->getUid()) {
                    return $this->loadUserByUsername($username);
                }
            }
        }

        $userInfo = array(
                    'service' => $token->getService(),
                    'uid' => $token->getUid(),
                    'username' => $token->getUsername(),
                    'accessToken' => $token->getCredentials(),
                    'password' => '',
                    'email' => $token->getEmail(),
                    'roles' => array('ROLE_USER'),
                    'enabled' => true,
                    'userNonExpired' => true,
                    'credentialsNonExpired' => true, 
                    'userNonLocked' => true
                    );
        $muser = new User($app, $userInfo);
        $app['monolog']->debug("loadUserByOAuth: service = " . $muser->getService());
        $app['monolog']->debug("loadUserByOAuth: uid = " . $muser->getUid());
        if ( $muser->doesExist() ) { 
            $muser->loadSelfFromDB();
            $this->createUser($muser);
            $app['monolog']->debug("loadUserByOAuth: user found in mongo");
        } else {
            $app['monolog']->debug("loadUserByOAuth: user not found in mongo");
            $userid = $muser->create();
            if ( !$userid ) {
                $app['monolog']->debug("loadUserByOAuth: user creation failed");
                return null;
            } else {
                $app['monolog']->debug("loadUserByOAuth: user creation success! id = " . $userid);
            }
        }
        // This is to prevent silex from serialization error, when storing $muser to memory
        $muser->detachApp();

        return $muser;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === '\\SilexMonkey\\Models\\User';
    }
}
