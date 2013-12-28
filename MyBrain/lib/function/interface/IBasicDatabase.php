<?php
/**
 * @name IBasicDatabase.php
 * Interface for Databases.
 * Dev-start: 2.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.3
 */
interface IBasicDatabase
{
	/**
	 * Get value by resource locator.
	 * @param array $resource_locator
	 * @return string $value: on success. || boolean false: on error.
	 * @example
	 * $ex = getValue(array('path','to','value'));
	 * echo $ex; // 'this is a test' (See setValue())
	 */
	public function getValue($resource_locator);
	
	/**
	 * Get value by resource locator as array.
	 * One element/line in value string.
	 * @param array $resource_locator
	 */
	public function getValueAsArray($resource_locator);
	
	/**
	 * Save value for resource locator.
	 * @param array $resource_locator
	 * @param string $value
	 * @return boolean $success
	 * @example setValue(array("path","to","value"), 'this is a test');
	 */
	public function saveValue($resource_locator, $value);
	
	/**
	 * Save values from array by resource locator.
	 * One element/line in value. Using newline (\n) in array values
	 * WILL lead to newlines in value. Use saveObject() in order to
	 * store Arrays.
	 * @param array $resource_locator
	 * @param array $value_array
	 */
	public function saveValueFromArray($resource_locator, $value_array);
	
	/**
	 * Get object by resource locator.
	 * @param array $resource_locator
	 * @return mixed $value: on success. || boolean false: on error.
	 */
	public function getObject($resource_locator);
	
	/**
	 * Save object for resource locator.
	 * @param string $resource_locator
	 * @param mixed $object
	 */
	public function saveObject($resource_locator, $object);
	
	/**
	 * Geta a Database Object with this resource locator as root.
	 * @param array $resource_locator
	 * @return IBasicDatabase $chrooted_db
	 */
	public function chroot($resource_locator);
}
