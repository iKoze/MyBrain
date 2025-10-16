<?php
class CError extends BasicFileController
{
	public function run()
	{
		$code = $this->getSiteName();
		if ($code == 401)
		{
			header("HTTP/1.0 401 Unauthorized! GTFO!");
		}
		else if ($code == 403)
		{
			header("HTTP/1.0 403 Forbidden! GTFO!");
		}
		else if ($code == 404)
		{
			header("HTTP/1.0 404 Not Found! WTF?");
		}
		else if ($code == 503)
		{
			header("HTTP/1.0 503 Service Unavailable! Oh no!");
		}
		else
		{
			header("HTTP/1.0 500 Internal Server Error! Boom!");
		}
	}

	public function getReturnCode()
	{
		$code = $this->getSiteName();
		if ($code == 401 || $code == 403 || $code == 404 || $code = 503) return $code;
		return 500;
	}	
}
