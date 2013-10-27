<?php

namespace SilexMonkey\Controllers;

use Silex\Application;

/**
 * Base class for application controllers.
 *
 * @author Monkey <leong_kui@yahoo.com>
 */
abstract class BaseController
{
    protected $app;
    protected $data;

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app=null)
    {
        $this->injectApp($app);
    }

    public function injectApp(Application $app=null)
    {
        $this->app = $app;
    }

    protected function constructHeader()
    {
        if ( !empty($this->app['user']) )  {
            $this->data['username'] = $this->app['user']->getUsername();
            $this->data['logoutCsrf'] = $this->app['form.csrf_provider']->generateCsrfToken('logout');
        }
        if ($this->app['security']->isGranted('ROLE_ADMIN')) {
            $this->data['isAdmin'] = true;
        }
    }

    protected function constructFooter()
    {
        $this->data['companyName'] = $this->app['config']['application']['companyName'];
        $this->data['year'] = date('Y');
    }

    public function render($template)
    {
        $this->constructHeader();
        $this->constructFooter();
        return $this->app['mustache']->render($template, $this->data); 
    }
}