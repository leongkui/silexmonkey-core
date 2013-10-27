<?php

namespace SilexMonkey\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ElasticaProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app['elastica'] = $app->share(function () use ($app) {
        	return new \Elastica\Client(array(
					'servers' => $app['elastica.servers']
				)
        	);
        });
    }
}
