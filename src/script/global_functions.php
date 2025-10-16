<?php
// Functions
function is_cli()
{
    return php_sapi_name() === 'cli';
}

function isset_or_null($array, $key)
{
	return isset($array[$key]) ? $array[$key] : null;
}

function get_isset_or_null($name)
{
#	if (is_cli())
#	{
#		global $argv;
#	}
	return isset($_GET[$name]) ? $_GET[$name] : null;
}

function post_isset_or_null($name)
{
	return isset($_POST[$name]) ? $_POST[$name] : null;
}

function debug($message)
{
	global $Braindebug;
	$Braindebug .= $message;
}

function debugln($message)
{
	global $Braindebug;
	$Braindebug .= $message."\n";
}

# thx: http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
function startsWith($haystack, $needle)
{
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle)
{
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function cliOrRewrite()
{
	if (
		is_cli() ||
		(function_exists("apache_get_modules") &&
		in_array('mod_rewrite', apache_get_modules())
		)
	) return true;
	return false;
}
