<?php 

$start = microtime(true);
$app = new Silex\Application();

$app['start'] = $start;
$app['debug'] = (getenv('APP_DEBUG') == 0)? false : true;
$app['env'] = getenv('APP_ENV') ?: 'prod';
$app['app_path'] = dirname(__DIR__) ;

$app['request'] = Symfony\Component\HttpFoundation\Request::createFromGlobals();;

//Override the Silex ControllerResolver to allow $app injection if the controller has a injectApp method
$app['resolver'] = $app->share(function () use ($app) {
    return new SilexMonkey\ControllerResolver($app, $app['logger']);
});

$app->register(
    new Herrera\Wise\WiseServiceProvider(),
    array(
        'wise.cache_dir' => $app['app_path']. '/cache/config',
        'wise.path' => $app['app_path'] . '/config/app/'.$app['env'],
        'wise.options' => array(
            'type' => 'yml',
            'mode' => $app['env'],
            'config' => array(
                'services' => 'services',
                'routes' => 'routes',
                ),
            'parameters' => $app
        )
    )
);

$app['config'] = $app['wise']->load('app.yml');
$app['search'] = $app['wise']->load('search.yml')['search'];
$app['oauth'] = $app['wise']->load('oauth.yml');
//Register service provider into Silex: config/<env>/services.yml
Herrera\Wise\WiseServiceProvider::registerServices($app);
//Register Routes: config/<env>/routes.yml
Herrera\Wise\WiseServiceProvider::registerRoutes($app);

//$app['session.storage.handler'] = $app->share(function ($app) {
    //return new Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler();
//});

$app->register(new Silex\Provider\SessionServiceProvider());
// Not to use default Silex Session (symfony) session handler, use PHP default
$app['session.storage.handler'] = null;
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app['security'] = array(
    'security.firewalls' => array(
        'default' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'oauth' => array(
                'login_path' => '/user/{service}/login',
                'callback_path' => '/user/{service}/auth',
                'check_path' => '/user/{service}/check',
                'failure_path' => '/user/login',
                'with_csrf' => true
            ),
            'logout' => array(
                'logout_path' => '/user/logout',
                'with_csrf' => true
            ),
            'users' => new SilexMonkey\Provider\OAuthUserProvider()
        )
    ),
    'security.access_rules' => array(
        array('^/auth', 'ROLE_USER'),
        array('^/admin', 'ROLE_ADMIN')
    )
);
$app->register(new Silex\Provider\SecurityServiceProvider(), $app['security']);
$app->register(new Gigablah\Silex\OAuth\OAuthServiceProvider(), $app['oauth']);
$app['oauth.user_provider_listener'] = $app->share(function ($app) {
    $thisListener = new SilexMonkey\Provider\UserProviderListener();
    $thisListener->setApp($app);
    return $thisListener;
});
$app->before(function (Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $token = $app['security']->getToken();
    $app['user'] = null;

    if ($token && !$app['security.trust_resolver']->isAnonymous($token)) {
        $app['user'] = $token->getUser();
    }
});

$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->after(function (Symfony\Component\HttpFoundation\Request $request,Symfony\Component\HttpFoundation\Response $response) use ($app) {
    $serverString = "<!-- " . $_SERVER['SERVER_ADDR'] . " : " . $_SERVER['SERVER_NAME'] . '-->' ;
    $response->setContent($response->getContent() . $serverString );
});

$app->finish(function () use ($app) {
    $duration = microtime(true) - $app['start'];
    $app['monolog']->addInfo("Request Duration=" . sprintf("%.4f ms", $duration));
});

return $app;
