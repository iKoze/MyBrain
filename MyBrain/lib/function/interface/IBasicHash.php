<?php
/**
 * @name IBasicHash.php
 * Interface for hash functions.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
interface IBasicHash
{
	/**
	 * Hashing function.
	 * @param string $input: String to hash
	 * @param string $salt: Salt used for hashing
	 * @return string $hash
	 */
	public function hash($input, $salt);
	
	/**
	 * Generate a suitable salt for the hash() function.
	 * @return string $salt
	 */
	public function generateSalt();
}
