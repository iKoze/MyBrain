<?php
/**
 * @name BlowFishHash.php
 * Class providing BlowFish hashing function.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class BlowFishHash implements BasicHash
{
	/**
	 * Returns Blowfish Hash.
	 * @see BasicHash::hash()
	 * @return string $hash: Blowfish hash.
	 */
	public static function hash($input, $salt)
	{
		$cryptsalt = '$2a$11$'.$salt.'$';
		return crypt($input, $cryptsalt);
	}
}