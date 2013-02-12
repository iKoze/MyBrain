<?php
/**
 * @name DBAuthentication.php
 * Provides database based authentication.
 * Dev-start: 12.2.2013 
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class DBAuthentication implements BasicAuthentication
{
	/**
	 * Contains Authentication Database Object
	 * @var BasicDatabase $authdb
	 * @example DB Layout
	 * |- 1.txt -> uid 1 -> contains string 'existent' for checking purposes
	 * |- 1 -> content of uid 1
	 *    |- salt.txt -> the stored salt for password hash
	 *    |- password.txt -> the stored password hash
	 */
	protected $authdb;
	
	/**
	 * The default separator for $authdb
	 * @var string $sep
	 */
	protected $sep;
	
	/**
	 * The name of the used class provides BasicHash
	 * @var string $hash_class
	 */
	protected $hash_class;
	
	/**
	 * New database based authentication.
	 * @param BasicDatabase $authdb
	 * @param string $hash_class: The BasicHash providing class.
	 */
	public function __construct(BasicDatabase $authdb, $hash_class)
	{
		$this->authdb = $authdb;
		$this->sep = $authdb->getSeparator();
		$refclass = new ReflectionClass($hash_class);
		
		// Check if given class implements BasicHash
		if($refclass->implementsInterface('BasicHash'))
		{
			$this->hash_class = $hash_class;
		}
		else
		{
			// or die!
			trigger_error("Passed Class ('".$hash_class."') doesn't implemlement BasicHash!", E_USER_ERROR);
		}
	}
	
	/**
	 * Check, if $pass is correct for $uid
	 * @param string $uid
	 * @param string $pass
	 * @return boolean $success: true on success
	 * @see BasicAuthentication::authenticate()
	 */
	public function authenticate($uid, $pass)
	{
		if($this->authdb->getValue($uid) !== false)
		{
			// User existent
			$salt = $this->getSalt($uid);
			$hashed_pw = $this->getHashedPassword($uid);
			$rehashed_pw = $this->hash($pass, $salt);
			if($hashed_pw == $rehashed_pw)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Set new password for $uid
	 * @param string $uid
	 * @param string $new_pass
	 * @return boolean
	 */
	public function setPassword($uid, $new_pass)
	{
		$salt = $this->generateSalt();
		$hashed_password = $this->hash($new_pass, $salt);
		$this->authdb->saveValue($uid.$this->sep.'salt', $salt);
		$this->authdb->saveValue($uid.$this->sep.'password', $hashed_password);
		$this->authdb->saveValue($uid, 'existent');
		return true;
	}
	
	/**
	 * Hash $pass with $salt using $this->hash_class
	 * @param string $pass
	 * @param string $salt
	 * @return string $hashed_password
	 */
	protected function hash($pass, $salt)
	{
		return call_user_func(array($this->hash_class, 'hash'), $pass, $salt);
	}
	
	/**
	 * Generate a new Salt using generateSalt function from $this->hash_class
	 * @return string $salt
	 */
	protected function generateSalt()
	{
		return call_user_func(array($this->hash_class, 'generateSalt'));
	}
	
	/**
	 * Get stored salt for $uid
	 * @param string $uid
	 * @return string $stored_salt
	 */
	protected function getSalt($uid)
	{
		return $this->authdb->getValue($uid.$this->sep.'salt');
	}
	
	/**
	 * Get the hashed password stored for $uid
	 * @param string $uid
	 * @return string $hashed_password
	 */
	protected function getHashedPassword($uid)
	{
		return $this->authdb->getValue($uid.$this->sep.'password');
	}
}