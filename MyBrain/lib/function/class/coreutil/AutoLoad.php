<?php
/**
 * @name AutoLoad.php
 * Class for handling autoload paths.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class AutoLoad
{
	/**
	 * All paths to search Classes within.
	 * @var array 
	 */
	private static $paths = array();
	
	/**
	 * New Autoloade. Registers function autoLoader() in spl_autoload_register. 
	 */
	public static function register()
	{
		spl_autoload_register('AutoLoad::autoLoader');
	}
	
	/**
	 * Adds a search path to autoloader. Without trailing slash.
	 * @param string $path
	 */
	public static function addPath($path)
	{
		array_push(self::paths, $path);
	}
	
	/**
	 * The autoload function itself. Will be invoked automatically by spl autoloader.
	 * @param string $class: The name of the class searched for.
	 * @return boolean $success: Could be found or not.
	 */
	public static function autoLoader($class)
	{
		foreach(self::paths as $path)
		{
			$expected_name = $path.DIRECTORY_SEPARATOR.$class.'.php';
			if(file_exists($expected_name))
			{
				include_once $expected_name;
				return true;
			}
		}
		return false;
	}
}