<?php
/**
 * @name BlowFishHash.php
 * Class providing BlowFish hashing function.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.2
 */
class BlowFishHash implements IBasicHash
{
	/**
	 * Cost of BlowFish hashing function
	 * Must be between 4 and 31
	 * @var number $cost
	 */
	protected $cost;

	/**
	 * New BlowFishHash
	 * @param number $cost: range: 4-31
	 */
	public function __construct($cost=11)
	{
		$this->cost = $cost;
	}

	/**
	 * Returns Blowfish Hash.
	 * @see BasicHash::hash()
	 * @return string $hash: Blowfish hash.
	 */
	public function hash($input, $salt)
	{
		return crypt($input, $salt);
	}
	
	/**
	 * @see BasicHash::generateSalt()
	 * @return string $salt
	 */
	public function generateSalt()
	{
		# Thanks to http://www.martinstoeckli.ch/php/php.html#bcrypt
		$length = 22; # Salt String length
		$binaryLength = (int)($length * 3 / 4 + 1);
		$randomBinaryString = mcrypt_create_iv($binaryLength, MCRYPT_DEV_URANDOM);
		$randomBase64String = base64_encode($randomBinaryString);
		$salt = str_replace('+', '.',substr($randomBase64String, 0, $length));
		if (version_compare(PHP_VERSION, '5.3.7') >= 0)
		{
			$algorithm = '2y';
		}
    	else
		{
      		$algorithm = '2a';
		}
		return '$'.$algorithm.'$'.$this->cost.'$'.$salt.'$';	
	}
}
