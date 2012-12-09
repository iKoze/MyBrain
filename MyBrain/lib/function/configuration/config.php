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

// Create new autoloader.
$autoloader = new AutoLoad();

// Prime the autoloader with knowledge of our include paths.
$autoloader->addPath('class');
$autoloader->addPath('class'.DS.'coreutil');
$autoloader->addPath('class'.DS.'optutil');
$autoloader->addPath('class'.DS.'plugins');
$autoloader->addPath('configuration');
$autoloader->addPath('interface');

// register the autoloader.
$this->registerModule('autoloader', $autoloader);

// Use databases.
$dbholder = new DatabaseHolder();

// Register the database holder.
$this->registerModule('dbholder', $dbholder);