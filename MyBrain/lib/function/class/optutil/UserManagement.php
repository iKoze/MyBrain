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
	 * @param number $uid
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
	 * @param number $uid
	 * @return User $user
	 */
	protected function spawnUser($uid)
	{
		$user = new User($uid, $this);
		$auth_method = $this->userdb->getValue($uid.$this->sep.'auth_method');
		$user->setAuthMethod($this->authholder->getInstane($auth_method));
		$user->setDatabase($this->userdb->chroot($uid.$this->sep.'db'));
		return $user;
	}
	
	/**
	 * Get Username by UID
	 * @param number $uid
	 * @return string $username
	 */
	public function getUsernameByUid($uid)
	{
		$username = $this->getArraySortedByUid();
		return $username[$uid];
	}
	
	/**
	 * Get User ID by Username
	 * @param string $username
	 * @return number $uid
	 */
	public function getUidByUsername($username)
	{
		$uid = $this->getArraySortedByUsername();
		return $uid[$username];
	}
	
	/**
	 * Gets the next free User ID
	 * @return number $free_uid
	 */
	public function getFreeUid()
	{
		$used = array_keys($this->getArraySortedByUid());
		sort($used, SORT_NUMERIC);
		return end($used) + 1;
	}
	
	/**
	 * Gets an array containing all uids, indexed by usernames
	 * @return array $uid
	 */
	protected function getArraySortedByUsername()
	{
		$uid = array();
		foreach($this->getUserIndexAsArray() as $line)
		{
			$uid[$line[1]] = $line[0];
		}
		return $uid;
	}
	
	/**
	 * Gets an array containing all usernames, indexed by uids.
	 * @return array $username
	 */
	protected function getArraySortedByUid()
	{
		$username = array();
		foreach($this->getUserIndexAsArray() as $line)
		{
			$username[$line[0]] = $line[1];
		}
		return $username;
	}
	
	/**
	 * Returns the user index as array.
	 * @return array $userindex
	 */
	protected function getUserIndexAsArray()
	{
		$content = $this->userdb->getValueAsArray('users');
		$userindex = array();
		foreach($content as $line)
		{
			$cur = explode(';',$line);
			array_push($userindex,$cur);
		}
		return $userindex;
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