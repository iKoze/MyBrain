<?php
/**
 * @name workset.php
 * Entry Point. Loads MyBrain.
 * 
 * Include this File in your index.php!
 * 
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */

// Path to MyBrain.php
require dirname(__FILE__).DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'coreutil'.DIRECTORY_SEPARATOR.'MyBrain.php';

// Initialize Brain
$Brain = new MyBrain(dirname(__FILE__),'configuration'.DIRECTORY_SEPARATOR.'config.php');

// do something useful here!