<?php
/**
 * @name BasicHash.php
 * Interface for hash functions.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
interface BasicHash
{
	/**
	 * Hashing function.
	 * @param string $input: String to hash.
	 * @param string $salt: Salt for the function.
	 * @return string $hash
	 */
	public static function hash($input, $salt);
}