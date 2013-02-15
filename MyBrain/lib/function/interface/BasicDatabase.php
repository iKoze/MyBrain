<?php
/**
 * @name BasicDatabase.php
 * Interface for Databases.
 * Dev-start: 2.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.2
 */
interface BasicDatabase
{
	/**
	 * Get value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @return string $value: on success. || boolean false: on error.
	 * @example
	 * $ex = getValue('path.to.value');
	 * echo $ex; // 'this is a test' (See setValue())
	 */
	public function getValue($resource_locator);
	
	/**
	 * Get value by resource locator as array.
	 * One element/line in value
	 * @param string $resource_locator
	 */
	public function getValueAsArray($resource_locator);
	
	/**
	 * Save value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @param string $value: Set field to $value.
	 * @return boolean $success: True on success, false on error.
	 * @example setValue('path.to.value', 'this is a test');
	 */
	public function saveValue($resource_locator, $value);
	
	/**
	 * Save values from array by resource locator.
	 * One element/line in value. Using newline (\n) in array values
	 * WILL lead to newlines in value. Use saveObject() in order to
	 * store Arrays.
	 * @param unknown $resource_locator
	 * @param unknown $value_array
	 */
	public function saveValueFromArray($resource_locator, $value_array);
	
	/**
	 * Get object by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 */
	public function getObject($resource_locator);
	
	/**
	 * Save object by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @param mixed $object: The object to save.
	 */
	public function saveObject($resource_locator, $object);
	
	/**
	 * Geta a Database Object with this resource locator as root.
	 * @param string $resource_locator
	 */
	public function chroot($resource_locator);
	
	/**
	 * Get the separator for resource locator.
	 * @return string $separator.
	 */
	public function getSeparator();
}