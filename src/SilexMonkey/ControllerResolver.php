<?php

namespace SilexMonkey;

use Silex\ControllerResolver as BaseControllerResolver;
use SilexMonkey\Controllers\BaseController as BaseController;

// Extends from Silex Controller Resolver, to inject app container if the class has injectApp method and is a controller
class ControllerResolver extends BaseControllerResolver
{
	protected function createController($controller)
	{
		list($thisController, $method) = parent::createController($controller);
		if ($thisController instanceof BaseController) {
			$thisController->injectApp($this->app);
		}
		return array($thisController,$method);
	}
}