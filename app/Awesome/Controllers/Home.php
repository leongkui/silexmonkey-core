<?php

namespace Awesome\Controllers;

use SilexMonkey\Controllers\BaseController;

class Home extends BaseController
{
	public function index()
	{
		return $this->render('index');
	}

	public function contact()
	{
		return $this->render('contact');
	}

	public function about()
	{
		return $this->render('about');
	}
}
