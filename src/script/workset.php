<?php
/**
 * @file
 * @brief Entry point. Loads MyBrain.
 * @date
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * 
 * Include this file in your index.php!
 *
 * What's happening here?
 * ======================
 * -# setting the working directory to the dir of this file
 * -# loading of global_functions.php using require()
 * -# initialisation of MyBrain
 * -# calling Brain's think() on request.php
 * 
 */

// Path to MyBrain.php
chdir(dirname(__FILE__));

// Loading Public functions
require_once("global_functions.php");

// Initialize Brain
require_once('class/core/MyBrain.php');
$Brain = new MyBrain('configuration/config.php');

$Brain->think("request.php");

