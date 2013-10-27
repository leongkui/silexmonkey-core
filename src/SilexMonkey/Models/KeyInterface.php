<?php

namespace SilexMonkey\Models;

interface KeyInterface
{
	public function set($key,$value);
	public function get($key);
	public function unset($key);
}