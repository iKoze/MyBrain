<?php
class User
{
	/**
	 * Whether the user is authenticated or not
	 * @var boolean $authenticated
	 */
	protected $authenticated = false;
	
	/**
	 * The User ID
	 * @var string $uid
	 */
	protected $uid;
	
	/**
	 * Authentication Methods
	 * @var array $auth_methods
	 * @example
	 * $auth_methods['auth_passdb'] = true;
	 */
	protected $auth_methods;
	
	/**
	 * User Properties
	 * @var array $properties
	 * @example
	 * $properties['email'][0] = true; // Information is private.<br>
	 * $properties['email'][1] = 'test(at)example.com'; // Information itself.
	 */
	protected $properties;
	
	/**
	 * New User
	 * @param string $uid
	 * User ID
	 */
	public function __construct($uid)
	{
		$this->uid = $uid;
	}
	
	/**
	 * Adds authentication method. 
	 * @param string $auth_name
	 * Class-Name of auth method.
	 * @param boolean $enabled
	 * Enabled? (Default: false)
	 */
	public function addAuthMethod($auth_name, $enabled = false)
	{
		$this->auth_methods[$auth_name] = $enabled;
	}
	
	/**
	 * Enables authentication method
	 * @param string $auth_name
	 * Class-Name of auth method.
	 */
	public function enableAuthMethod($auth_name)
	{
		$this->auth_methods[$auth_name] = true;
	}
	
	/**
	 * Disables authentication method
	 * @param string $auth_name
	 * Class-Name of auth method.
	 */
	public function disableAuthMethod($auth_name)
	{
		$this->auth_methods[$auth_name] = false;
	}
	
	/**
	 * Removes authentication method
	 * @param string $auth_name
	 * Class-Name of auth method.
	 */
	public function removeAuthMethod($auth_name)
	{
		unset($this->auth_methods[$auth_name]);
	}
	
	/**
	 * Authenticates User
	 * @param BasicAuthentication $auth
	 * Authentication Object.
	 * @param string $token
	 * Token for Authentication Object. May be Password or Session ID
	 * @return boolean: success.
	 */
	public function authenticate(BasicAuthentication $auth, $token)
	{
		if(isset($this->auth_methods[get_class($auth)]) && $this->auth_methods[get_class($auth)] === true)
		{
			// Auth method allowed
			if($auth->authenticate($this->uid, $token))
			{
				$this->authenticated = true;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns whether user is authenticated or not.
	 * @return boolean
	 */
	public function isAuthenticated()
	{
		return $this->authenticated;
	}
	
	/**
	 * Revokes Authentication.
	 */
	public function revokeAuthentication()
	{
		$this->authenticated = false;
	}
	
	/**
	 * Change User ID.
	 * @param string $new_uid
	 * @return boolean success
	 */
	public function changeUID($new_uid)
	{
		if($this->isAuthenticated())
		{
			$this->uid = $new_uid;
			return true;
		}
		return false;
	}
	
	/**
	 * Change or add property to user
	 * @param string $prop_name
	 * Name of the property
	 * @param boolean $private
	 * Is the property private?
	 * @param string $content
	 * Content of the property
	 * @return boolean $success
	 */
	public function changeProperty($prop_name, $private, $content)
	{
		if($this->isAuthenticated())
		{
			$this->properties[$prop_name][0] = $private;
			$this->properties[$prop_name][1] = $content;
			return true;
		}
		return false;
	}
	
	/**
	 * Get property by name
	 * @param string $prop_name
	 * @return string $content || false if not authenticated and private.
	 */
	public function getProperty($prop_name)
	{
		if($this->isAuthenticated() || $this->properties[$prop_name][0] === false)
		{
			return $this->properties[$prop_name][1];
		}
		return false;
	}
	
	/**
	 * Revoke Authentication for this Object, before destroying it.
	 */
	public function __destruct()
	{
		$this->revokeAuthentication();
	}
}