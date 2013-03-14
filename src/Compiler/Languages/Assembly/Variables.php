<?php namespace Compiler\Languages\Assembly;

class Variables {

	protected $variables = array();

	protected $arrays = array();

	public function exists($key)
	{
		return isset($this->variables[$key]);
	}

	public function create($key, $value = 0)
	{
		if( ! $this->exists($key))
		{
			$this->variables[$key] = $value;
		}
	}

	public function set($key, $value)
	{
		$this->variables[$key] = $value;
	}

	public function get($key = null)
	{
		if(is_null($key))
		{
			return $this->variables;
		}

		else
		{
			return $this->variables[$key];
		}
	}

	public function getArrays()
	{
		return $this->arrays;
	}

	public function arrayExists($array)
	{
		return isset($this->arrays[$array]);
	}

	public function createArray($array)
	{
		$this->arrays[$array] = array();
	}

	public function addArrayElement($array, $value = 0)
	{
		$this->arrays[$array][] = $value;
	}

	public function setArrayElement($array, $key, $value = 0)
	{
		$this->arrays[$array][$key] = $value;
	}

	public function getArrayElement($array, $key)
	{
		return $this->arrays[$array][$key] = $value;
	}
}