<?php namespace Compiler\Languages\Variables;

class Variables {

	/**
	 * The currently defined language variables.
	 *
	 * @var array
	 */
	protected $variables = array();

	/**
	 * The currently defined language arrays.
	 *
	 * @var array
	 */
	protected $arrays = array();

	/**
	 * Verify that a variable is set.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function exists($key)
	{
		return isset($this->variables[$key]);
	}

	/**
	 * Create a new variable or array.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  bool    $array
	 * @return void
	 */
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

	/**
	 * Create an array.
	 *
	 * @param  string  $key
	 * @param  array   $value
	 * @return void
	 */
	public function createArray($key, array $values = array())
	{
		$this->create($key, $values, true);
	}

	/**
	 * Set the value to a variable.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set($key, $value)
	{
		$this->variables[$key] = $value;
	}

	/**
	 * Get the value of a variable. Returns all variables
	 * if no key is passed.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
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