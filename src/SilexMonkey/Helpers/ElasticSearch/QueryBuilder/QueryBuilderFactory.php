<?php

namespace SilexMonkey\Helpers\ElasticSearch\QueryBuilder;

class QueryBuilderFactory {
	public static function create($app, $type, $queryParameters)
	{
		$class = $app['search'][$type]['queryBuilder'];
		$thisClass = new $class();
		$thisClass->build($queryParameters);
		return $thisClass;
	}	
}
