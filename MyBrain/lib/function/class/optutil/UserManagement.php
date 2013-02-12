<?php
/**
 * @name UserManagement.php
 * Provides user management.
 * Dev-start: 12.2.2013 
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class UserManagement
{
	/**
	 * Contains User Database Object
	 * @var BasicDatabase $userdb
	 * @example DB Layout
	 * |- users.txt -> csv -> uid,username
	 * |
	 * |- 1 -> content of uid 1
	 *    |- auth_method.txt -> current auth method for uid 1
	 *    |- db -> Database passed to user
	 */
	protected $userdb;
	
	/**
	 * Contains all possible authentication methods.
	 * @var InstanceHolder $authholder
	 */
	protected $authholder;
	
	/**
	 * The default separator for $userdb
	 * @var string $sep
	 */
	protected $sep;
	
	/**
	 * New user management.
	 * @param BasicDatabase $userdb
	 * @param InstanceHolder $authholder: all possible authentication methods.
	 */
	public function __construct(BasicDatabase $userdb, InstanceHolder $authholder)
	{
		$this->userdb = $userdb;
		$this->sep = $userdb->getSeparator();
		$this->authholder = $authholder;
	}
	
	// TODO add an addUser function ;)
	
	/**
	 * Get user by User ID
	 * @param string $uid
	 * @return User $user || false on error
	 */
	public function getUserByUid($uid)
	{
		// Check if user is existent
		if($this->userdb.getValue($uid) === false)
		{
			// User not existent
			return false;
		}
		
		return $this->spawnUser($uid);
		// return user;
	}
	
	/**
	 * Create new User object for existing by it's UID from database.
	 * @param string $uid
	 * @return User $user
	 */
	protected function spawnUser($uid)
	{
		$user = new User($uid, $this);
		$auth_method = $this->userdb->getValue($uid.$this->sep.'auth_method');
		$user->setAuthMethod($this->authholder->getInstane($auth_method));
		
		// TODO: This is ugly. Replace it by adding fancy chroot function to BasicDatabase
		$userdbpath = $this->userdb->getSubPathByRl($uid.$this->sep.'db');
		$user->setDatabase(new FileDB($userdbpath));
		return $user;
	}
	
	/**
	 * Saves settings of user object back to userdb.
	 * @param User $user
	 * @return boolean $success = true
	 */
	public function saveMe(User $user)
	{
		$uid = $user->getUid();
		$auth_method = $user->getAuthMethodName();
		$this->userdb->saveValue($uid.$this->sep.'auth_method', $auth_method);
		return true;
	}
}