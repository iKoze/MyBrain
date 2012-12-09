<?php
/**
 * @name DatabaseHolder.php
 * Class for storing Databases references by name in an array.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class DatabaseHolder
{
	/**
	 * Contains all stored database references.
	 * @var array $databases
	 */
	private $databases = array();
	
	/**
	 * Add a database.
	 * @param string $name: Name of database.
	 * @param BasicDatabase $database: Database reference.
	 * @return boolean: true on success.
	 */
	public function addDatabase($name, $database)
	{
		if($database instanceof BasicDatabase && !isset($this->databases[$name]))
		{
			$this->databases = $database;
			return true;
		}
		return false;
	}
	
	/**
	 * Returns reference of database by given name.
	 * @param string $name: Name of database.
	 * @return BasicDatabase: The stored database.
	 */
	public function getDatabase($name)
	{
		return $this->databases[$name];
	}
	
	/**
	 * Returns all known databases as array.
	 * @return array $databases:
	 */
	public function getAllDatabases()
	{
		return $this->databases;
	}
}