<?php
/**
 * @name BasicAuthentication.php
 * Authentication Interface
 * @author Florian Schiessl <florian@floriware.de>
 * @version: 0.1
 */
interface BasicAuthentication
{
	/**
	 * Authenticate User somewhere, somehow.
	 * @param string $uid: The user ID.
	 * @param string $token: May be the Password or Session-ID. 
	 * @return boolean $success: true if successful.
	 */
	public function authenticate($uid, $token);
}