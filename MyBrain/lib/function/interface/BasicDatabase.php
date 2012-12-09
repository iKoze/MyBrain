<?php
/**
 * @name BasicDatabase.php
 * Interface for Databases.
 * Dev-start: 2.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
interface BasicDatabase
{
	public function __construct();
	
	/**
	 * Get value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @return string $file_content: on success. || boolean false: on error.
	 * @example
	 * $ex = getValue('path.to.value');
	 * echo $ex; // 'this is a test' (See setValue())
	 */
	public function getValue($resource_locator);
	
	/**
	 * Set value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @param string $value: Set field to $value.
	 * @return boolean $success: True on success, false on error.
	 * @example setValue('path.to.value', 'this is a test');
	 */
	public function setValue($resource_locator, $value);
	
	public function __destroy();
}