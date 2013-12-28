<?php
/**
 * @name UserManagement.php
 * Provides user management.
 * Dev-start: 12.2.2013 
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.2
 */
class UserManagement
{
	/**
	 * Contains user database object
	 */
	protected $userdb;
	
	/**
	 * Contains hash providing object for password hashing
	 */
	protected $hash;
	
	/**
	 * New UserManagement
	 * @param IBasicDatabase $userdb: User Database
	 * @param IBasicHash $hash: Hash providing object used for password hashing
	 */
	public function __construct(IBasicDatabase $userdb, IBasicHash $hash)
	{
		$this->userdb = $userdb;
		$this->hash = $hash;
	}
	
	/**
	 * Create a new User
	 * @param string $uid: the user id (username)
	 * @param string $pass: the password
	 * @return boolean true: on success
	 */
	public function newUser($uid, $pass)
	{
		if ($this->uidExists($uid)) return false; # User already exists
		return $this->changePassword($uid,$pass); # Changing password for unexisting user means creating user
	}
	
	// TODO: add a deluser function
	
	/**
	 * Check if $pass is correct for $uid
	 * @param string $uid
	 * @param string $pass
	 * @return boolean true: if correct
	 */
	public function authenticate($uid, $pass)
	{
		if (!$this->uidExists($uid)) return false; # non-existing users cannot be authenticated
		$hashed_pw = $this->userdb->getValue(array($uid,"pass")); # get hashed password
		$entered_pw = $this->reHashPassword($uid,$pass); # re hash user entered password with stored salt
		return $hashed_pw === $entered_pw; # return true if password correct
	}
	
	/**
	 * Change $pass for $uid
	 * @param string $uid
	 * @param string $new_pass
	 * @return boolean true
	 */
	public function changePassword($uid, $new_pass)
	{
		$salt = $this->hash->generateSalt(); # Generate new password salt
		$hashed_pw = $this->hash->hash($new_pass,$salt); # hash password with salt
		$this->userdb->saveValue(array($uid,"salt"),$salt); # save salt
		$this->userdb->saveValue(array($uid,"pass"),$hashed_pw); # save hashed password
		return true;
	}
	
	/**
	 * Get a user associated database
	 * @param string $uid
	 * @return IBasicDatabase $user_db
	 */
	public function getUserDb($uid)
	{
		$this->userdb->saveValue(array($uid,"data","dummy"),null); # Creating userdb if unexisting
		return $this->userdb->chroot(array($uid,"data"));
	}
	
	/**
	 * Re hash $pass with stored salt from $uid
	 * @param string $uid
	 * @param string $pass
	 * @return string $rehashed_password
	 */
	public function reHashPassword($uid, $pass)
	{
		$salt = $this->userdb->getValue(array($uid,"salt")); # get stored salt
		return $this->hash->hash($pass,$salt); # re hash password with salt
	}
	
	/**
	 * Check if $uid already exists
	 * @param string $uid
	 * @return boolean true: if exists.
	 */
	public function uidExists($uid)
	{
		return $this->userdb->getValue(array($uid,"pass")) !== false;
	}
}
