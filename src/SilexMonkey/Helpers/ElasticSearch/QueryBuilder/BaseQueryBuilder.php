<?php

namespace SilexMonkey\Helpers\ElasticSearch\QueryBuilder;

use \Elastica\Filter\Term;
use \Elastica\Filter\BoolAnd;
use \Elastica\Filter\BoolOr;

class BaseQueryBuilder
{
	protected $queryString = null;
	protected $query = null;
	protected $operator = 'OR';
	protected $limit = 20;
	protected $offset = 0;

	public function __construct()
	{
		$this->queryString = new \Elastica\Query\QueryString();
		$this->query = new \Elastica\Query();
	}

	public function build($queryParameters)
	{
		//'And' or 'Or' default : 'Or'
		$this->queryString->setDefaultOperator($this->operator);
		$this->queryString->setQuery($queryParameters['search']);
		
		// Create the actual search object with some data.
		$this->query->setQuery($this->queryString);
		$this->query->setLimit($this->limit);
		$this->query->setFrom($this->offset);
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function setLimit($limit)
	{
		$this->query->setLimit($limit);
	}

	public function setOffset($offset)
	{
		$this->query->setFrom($offset);
	}
}
