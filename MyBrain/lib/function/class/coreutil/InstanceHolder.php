<?php
/**
 * @name InstanceHolder.php
 * Class for storing instance references of single types.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class InstanceHolder
{
	/**
	 * Contains all stored instances references.
	 * @var array $instances
	 */
	protected $instances = array();
	
	/**
	 * Contains name of the interface, which stored instances are using.
	 * @var string $interface
	 */
	protected $interface;
	
	/**
	 * New InstanceHolder
	 * @param string $interface: The interface, which stored instances must use.
	 */
	public function __construct($interface)
	{
		$this->interface = $interface;
	}
	
	/**
	 * Returns the name of the interface, which stored instances are using.
	 * @return string $interface
	 */
	public function getInterface()
	{
		return $this->interface;
	}
	
	/**
	 * Add a instance.
	 * @param string $name: Name of instance.
	 * @param object $instance: instance reference.
	 * @return boolean: true on success.
	 */
	public function addInstance($name, $instance)
	{
		if($instance instanceof $this->interface && !isset($this->instances[$name]))
		{
			$this->instances[$name] = $instance;
			return true;
		}
		return false;
	}
	
	/**
	 * Returns reference of instance by given name.
	 * @param string $name: Name of instance.
	 * @return object: The stored instance.
	 */
	public function getInstance($name)
	{
		return $this->instances[$name];
	}
	
	/**
	 * Returns all known instances as array.
	 * @return array $instances:
	 */
	public function getAllInstances()
	{
		return $this->instances;
	}
}