<?php
/**
 * @name config.php
 * Example configuration file.
 * 
 * This file is included by MyBrain.php in __construct.
 * 
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.2
 */

// FileDB root
$fdbroot = '/var/cms-test';

// small shortcut to make things more easy.
define('DS',DIRECTORY_SEPARATOR);

// Load Autoloader's class.
require_once 'class'.DS.'coreutil'.DS.'AutoLoad.php';

// Prime the autoloader with knowledge of our include paths.
AutoLoad::addPath('class');
AutoLoad::addPath('class'.DS.'coreutil');
AutoLoad::addPath('class'.DS.'optutil');
AutoLoad::addPath('class'.DS.'plugins');
AutoLoad::addPath('configuration');
AutoLoad::addPath('interface');
AutoLoad::register();

// Use databases.
$dbholder = new InstanceHolder('BasicDatabase');
$userdb = new FileDB($fdbroot.DS.'UserDB');
$dbholder->addInstance('UserDB', $userdb);

// Add UserManagement
$usermanager = new UserManagement($userdb, new BlowFishHash(11));

// Register the Modules to MyBrain
$this->registerModule('dbholder', $dbholder);
$this->registerModule('UserManagement', $usermanager);
