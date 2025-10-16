<?php
/**
 * @name MyBrain.php
 * Main Class for loading and handling plugins.
 * Dev-start: 2.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
/**
 * MyBrain primary module
 * ======================
 * Register all modules that do something you'll need later here.
 * Primary meant for one instance objects which provide functionality.
 *
 * Examples include:
 * * Redis/Memcached cache connector
 * * SQL database connector
 * * Link creator/manager
 *
 * Why this?
 * ---------
 *
 * One goal of MyBrain is to keep the root namespace as clear as possible.
 * Per default only one variable is kept in the root namespace: $Brain.
 * (Notice the uppercase 'B'.) $Brain contains an instance of this class.
 * In your module you can use the getModule() function to get the module.
 * @todo later it'll be possible to 'wake up' the modules they're really 
 * required. This will get speed improvements since then you won't 
 * necessarily need to load every module for jobs where you don't need them.
 *
 * How?
 * ----
 *
 * Create instances of your modules in the request.php file. Then use
 * registerModule() to register your Module. Later, when you'll need your
 * module, call $Brain-> getModule().
 */
class MyBrain
{
	/**
	 * Contains all loaded Modules.
	 * @var array $modules
	 */
	private $modules = array();

	private $data = array();
	
	/**
	 * New MyBrain Module Holder
	 * @param string $config: Path to configuration File.
	 */
	public function __construct($config)
	{
		require($config);
	}
	
	/**
	 * Register a new module
	 * @param string $name: Name of the module.
	 * @param object $module: The module.
	 * @return boolean $success: true on success.
	 */
	public function registerModule($name, $module)
	{
		if (is_object($module) && !isset($this->modules[$name]))
		{
			$this->modules[$name] = $module;
			return true;
		}
		return false;
	}
	
	/**
	 * Get module by name.
	 * @param string $name: Name of the module
	 * @return object: The module.
	 */
	public function getModule($name)
	{
		return $this->modules[$name];
	}
	
	/**
	 * Returns all loaded modules as array.
	 * @retval array $modules;
	 */
	public function getAllModules()
	{
		return $this->modules;
	}

	/**
	 * @brief Register some data
	 * @param string $key: index key of the data
	 * @param mixed $data
	 */
	public function registerData($key, $data)
	{
		$this->data[$key] = $data;
	}

	/**
	 * @brief Get previously registered data from given key.
	 *
	 * Also takes objects as keys. In such case, the object's class name is used
	 * as key.
	 * @param mixed $key
	 * @return data 
	 */
	public function getData($key)
	{
		if (is_object($key))
		{
			return $this->data[get_class($key)];
		}
		return $this->data[$key];
	}

	/**
	 * @brief executes passed file name in MyBrain object.
	 * "thinks about it." He He.
	 * @note in the passed file, $this is the reference to MyBrain object.
	 */
	public function think($about)
	{
		require($about);
	}
}
