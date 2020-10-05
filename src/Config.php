<?php namespace Dish;

class Config {

	/**
	 * Data
	 *
	 * @var array
	 */

	public static $data = [];

	/**
	 * load
	 *
	 * Load the JSON file from src/
	 *
	 * @param	$value string
	 * @return	void
	 */

	public static function load($file)
	{
		$data = json_decode(file_get_contents($file));

		foreach($data as $key => $value)
		{
			self::$data[$key] = $value;
		}
	}

	/**
	 * Traverse an array and find a value
	 *
	 * @param	mixed
	 * @return	array
	 */

	public static function get($args)
	{
		$keys = self::args($args);
		$arr = self::$data;

		if((is_array($arr) && count($arr)) || is_object($arr))
		{
			foreach($keys as $key)
			{
				if(is_object($arr) && isset($arr->$key))
				{
					$arr = $arr->$key;
				}
				else if(is_array($arr) && isset($arr[$key]))
				{
					$arr = $arr[$key];
				}
				else
				{
					return [];
				}
			}

			return $arr;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Set
	 *
	 * Set a config value
	 *
	 * @param	$key	mixed
	 * @param	$key	mixed
	 * @return	void
	 */

	public static function set($key, $value)
	{
		self::$data[$key] = $value;
	}

	/**
	 * setMany
	 *
	 * Set a config value
	 *
	 * @param	$arr	array
	 * @return	void
	 */

	public static function setMany($arr)
	{
		foreach($arr as $key => $value)
		{
			self::$data[$key] = $value;
		}
	}

	/**
	 * Resolve the arguments into an array
	 *
	 * @param	array
	 * @return	array
	 */

	public static function args($args)
	{
		if(is_array($args) and count($args) == 1)
		{
			$args = $args[0];
		}

		if(is_array($args) and count($args) == 1)
		{
			$args = $args[0];
		}

		if(is_string($args) and substr_count((string)$args, '.'))
		{
			$args = explode('.', $args);
		}

		return (array)$args;
	}
}
