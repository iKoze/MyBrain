<?php
/**
 * @name User.php
 * The user's object
 * Dev-start: 12.2.2013
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class User
{
	/**
	 * The Users ID
	 * @var string $uid
	 */
	protected $uid;
	
	/**
	 * The user management object, responsible for this user.
	 * @var UserManagement $manager
	 */
	protected $manager;
	
	/**
	 * The authentication method used for this user.
	 * @var BasicAuthentication $auth_method
	 */
	protected $auth_method;
	
	/**
	 * The user's private database. Save user-associated data here.
	 * @var BasicDatabase $database
	 */
	protected $database;
	
	/**
	 * A new user object.
	 * @param string $uid
	 * @param UserManagement $manager: The user management object, responsible for this user.
	 */
	public function __construct($uid, UserManagement $manager)
	{
		$this->uid = $uid;
		$this->manager = $manager;
	}
	
	/**
	 * Get the user's id
	 * @return string $uid
	 */
	public function getUid()
	{
		return $this->uid;
	}
	
	/**
	 * Set the users authentication method.
	 * @param BasicAuthentication $auth_method
	 */
	public function setAuthMethod(BasicAuthentication $auth_method)
	{
		$this->auth_method = $auth_method;
	}
	
	/**
	 * Get the user's authentication method name.
	 * @return string
	 */
	public function getAuthMethodName()
	{
		return get_class($this->auth_method);
	}
	
	/**
	 * Check, if provided $password is valid for this user.
	 * @param string $password
	 */
	public function authenticate($password)
	{
		return $this->auth_method->authenticate($this->uid, $password);
	}
	
	/**
	 * Set a new Password for this user. (If provided by AuthMethod)
	 * @param string $new_password
	 * @return boolean $success
	 */
	public function setPassword($new_password)
	{
		if(method_exists($this->auth_method, 'setPassword'))
		{
			return $this->auth_method->setPassword($this->uid, $new_password);
		}
		return false;
	}
	
	/**
	 * Set the user's private database.
	 * @param BasicDatabase $userdb
	 */
	public function setDatabase(BasicDatabase $userdb)
	{
		$this->database = $userdb;
	}
	
	/**
	 * Get the user's private database.
	 * @return BasicDatabase
	 */
	public function getDatabase()
	{
		return $this->database;
	}
	
	/**
	 * Save the users basic settings back to userdb using the manager.
	 * Currently only necessary after change of AuthMethod.
	 * @see UserManagement.php: function SaveMe()
	 * @return boolean $success.
	 */
	public function save()
	{
		return $this->manager->saveMe($this);
	}
	
	/**
	 * Save the user before loosing all it's settings.
	 */
	public function __destruct()
	{
		$this->save();
	}
}