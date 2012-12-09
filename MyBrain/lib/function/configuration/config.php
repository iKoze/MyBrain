<?php
/**
 * @name config.php
 * Example configuration file.
 * 
 * This file is included by MyBrain.php in __construct.
 * 
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */

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

// Register the database holder.
$this->registerModule('dbholder', $dbholder);