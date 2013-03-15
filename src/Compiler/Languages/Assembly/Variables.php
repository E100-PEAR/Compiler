<?php namespace Compiler\Languages\Assembly;

class Variables {

	protected $variables = array();

	protected $arrays = array();

	public function exists($key)
	{
		return isset($this->variables[$key]);
	}

	public function create($key, $value = 0, $array = false)
	{
		if( ! $this->exists($key))
		{
			if($array)
			{
				$this->variables[$key] = new ArrayVariable($key, $value);
			}

			else
			{
				$this->variables[$key] = new Variable($key, $value);
			}
		}
	}

	public function createArray($key, array $values = array())
	{
		$this->create($key, $values, true);
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
}