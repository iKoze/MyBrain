<?php
/**
 * @name MyBrain.php
 * Main Class for loading and handling plugins.
 * Dev-start: 2.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class MyBrain
{
	/**
	 * Contains all loaded Modules.
	 * @var array $modules
	 */
	private $modules = array();
	
	/**
	 * New MyBrain Module Holder
	 * @param string $path: Root path to chdir to.
	 * @param string $config: Path to configuration File.
	 */
	public function __construct($path, $config)
	{
		chdir($path);
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
	 * @return array $modules;
	 */
	public function getAllModules()
	{
		return $this->modules;
	}
}