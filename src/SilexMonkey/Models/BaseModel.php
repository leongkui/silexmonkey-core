<?php

namespace SilexMonkey\Models;

abstract class BaseModel
{
	
	protected $app = null;
	protected $sourceData = array();

	protected $_data = array();

	private $error = '';

	public function __construct(\Silex\Application $app=null, $data = array())
	{
		if (!empty($app)){
			$this->app = $app;
		}

		if (!empty($data)) {
			$this->sourceData = $data;
		}
		$this->initBase($data);
		$this->initModel($data);
		$this->postInit();
	}

	protected function initBase($data)
	{
		/**
		* @codeCoverageIgnore
		*/
	}

	public function initModel($data)
	{
		foreach ( $data as $key => $value ) {
			if ($key === '_id') {
				continue;
			}
			$setProperty = 'set' . ucfirst($key);
			$this->{$setProperty}($value);
		}
	}

	protected function postInit()
	{
		/**
		* @codeCoverageIgnore
		*/
	}

	public function detachApp()
	{
		$this->app = null;
	}

	public function attachApp(\Silex\Application $app=null)
	{
		$this->app = $app;
	}

	public function getAttribute($attribute)
	{
		return $this->{$attribute};
	}

	public function setAttribute($attribute,$value)
	{
		$this->{$attribute} = $value;
	}

	public function unsetAttribute($attribute)
	{
		unset($this->{$attribute});
	}

	public function __call($method, $arguments)
	{
	    $prefix = strtolower(substr($method, 0, 3));
	    $property = lcfirst(substr($method, 3));

	    if (empty($prefix) || empty($property)) {
	        return;
	    }

	    if ($prefix == "get" && isset($this->{$property})) {
	        return $this->{$property};
	    } else if ($prefix == "get" && isset($this->_data[$property])) {
	        return $this->_data[$property];
	    }

	    if ($prefix == "set") {
	    	if ( property_exists($this, $property) ) { 
	     	   $this->{$property} = $arguments[0];
	    	} else {
	     	   $this->_data[$property] = $arguments[0];
	    	}
	    }
	}

	abstract public function doesExist();
}