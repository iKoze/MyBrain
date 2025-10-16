<?php
/**
 * @file
 * @author Florian Schiessl <florian@floriware.de>
 * @brief Root file. Everything starts here.
 *
 * What's happening here?
 * ======================
 * -# ROOT is defined by this file
 * -# workset.php gets called
 */
define('ROOT',dirname(__FILE__));
require(ROOT."/lib/function/workset.php");
