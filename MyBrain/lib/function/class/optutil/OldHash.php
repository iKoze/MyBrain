<?php
/**
 * @name OldHash.php
 * Class providing string hash function.
 * Dev-start: 9.12.2012.
 * @deprecated only here as example and for backward compatibility!
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class OldHash implements IBasicHash
{
	/**
	 * @see BasicHash::hash()
	 * @deprecated only here as example and for backward compatibility!
	 */
	public function hash($input, $salt)
	{
		$key = crypt($input, $salt); # Hashing
		# blew up
		for($i = 0; $i <= 10; $i++)
		{
			$key = crypt($key, $salt.$i);
		}
		return $key;
	}
	
	/**
	 * @see BasicHash::generateSalt()
	 * @return string $salt
	 */
	public function generateSalt()
	{
		// TODO Do something more useful here. This is just for testing.
		return "rkcurksotmhgqrynda74up";
	}
}
