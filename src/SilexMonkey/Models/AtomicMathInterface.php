<?php

namespace SilexMonkey\Models;

interface AtomicMathInterface
{
	public function increment($key, $increment=1);
	public function decrement($key, $decrement=1);
}