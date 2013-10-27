<?php

namespace SilexMonkey\Helpers\ElasticSearch\QueryBuilder;

use \Elastica\Filter\Term;
use \Elastica\Filter\BoolAnd;
use \Elastica\Filter\BoolOr;

class Cards extends BaseQueryBuilder
{
	public function addActiveOnlyFilter()
	{
		$esFilterActiveOnly = new Term();
		$esFilterActiveOnly->setTerm('status','active');
		$this->query->setFilter($esFilterActiveOnly);
	}
}
