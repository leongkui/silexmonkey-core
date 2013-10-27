<?php

namespace SilexMonkey\Helpers\ElasticSearch\ResultIterator;

class BaseResultIterator
{
	protected $resultSet;

	public function __constructor(\Elastica\ResultSet $result)
	{
		var_dump($result);
		$this->resultSet = $result;
	}

	public function getTotalHits()
	{
		var_dump($this->resultSet);
		return $this->resultSet->getTotalHits();
	}

	public function getArray()
	{
		$return = array();
		foreach($this->resultSet->getResults() as $thisResult) {
			$return[] = $thisResult->getData();
		}	
		return $return;
	}
}