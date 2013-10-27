<?php

namespace SilexMonkey\Models;

interface CRUDInterface
{
	public function create($data);
	public function retrieve($query);
	public function update($match,$change);
	public function delete($match);
}