<?php

namespace SilexMonkey\Models;

class MongoModel extends BaseModel implements CRUDInterface
{
	protected $collection = dummy;
	protected $mongo = null;
	protected $id = null;
	protected $_id = null;

	protected function initBase()
	{
		$dbname = $this->app['config']['mongo']['dbname'];
		$this->app['monolog']->addDebug("dbname = $dbname, Collection = " . $this->collection);
		$this->mongo = $this->app['mongo']['default']->selectCollection($dbname,$this->collection);	
		if(!empty($data['_id'])) {
			$this->_id = new \MongoId($data['_id']);
		}
	}

	protected function convertToArray(\MongoCursor $cursor)
	{
		return iterator_to_array($cursor, false);
	}

	public function create($data = array())
	{
		if (empty($data)) {
			if (empty($this->sourceData)) {
				return null;
			}
		} else {
			$this->sourceData = $data;
			$this->postInit();
		}

		try {
			$status = $this->mongo->insert($this->sourceData);
			if (isset($this->sourceData['_id'])) {
				$this->id = (string)$this->sourceData['_id'];
				$this->initModel($this->sourceData);
				return $this->id;
			} else {
				return null;
			}
		} catch (MongoCursorException $exception) {
			$this->error = $exception->getMessage();		
			return null;
		}
	}

	public function update($match,$change)
	{
		return $this->mongo->update($match, array('$set'=>$change));
	}
	
	public function delete($match)
	{
		return $this->mongo->remove($match);
	}
	
	public function retrieve($query)
	{
		return $this->convertToArray($this->mongo->find($query));
	}
	
	public function retrieveOne($query)
	{
		return $this->mongo->findOne($query);
	}
	
	public function findById(\MongoId $id)
	{
		return $this->mongo->findOne(array('_id'=>$id));
	}

	public function doesExist()
	{
	}
}