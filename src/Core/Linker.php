<?php
/**
 * @brief print links, check URL's and do redirects
 * @author Florian Schießl <florian@floriware.de> (23.4.2016)
 */
namespace Floriware\MyBrain\Core;

class Linker
{
	/** @brief URLDirector for this Linker */
	public $urld;

	public function __construct(URLDirector $urld)
	{
		$this->urld = $urld;
	}

	/** @brief check if an url exists within scope of $urld  */
	public function URLexists($url)
	{
		# TODO do check of HTTP code (200)
		return $this->urld->getURL($url) !== null;
	}

	/** @brief format a relative url to a absolute url depending on the environment 
	 * If cli or apache with mod rewrite:
	 *     /url/
	 * else:
	 *     /SCRIPT_NAME/url/
	 * @param $url if null or '.', use $Brain->getData("req-url")
	 * @param $get associative array for get parameters.
	 */
	public function getURL($url=null, $get=array())
	{
		global $Brain;
		# try to get result of url
		$url = strtolower($url);
		if ($url === null || $url == ".")
		{
			$url = $this->urld->clearifyURL($Brain->getData("req-url"));
		}

		$first = false;
		if (cliOrRewrite())
		{
			$url = "/".$url."/";
			if (count($get) > 0)
			{
				$url .= "?";
				$first = true;
			}
		}
		else
		{
			#$url = "/?".GET_SITE."=".$url;
			$url = $_SERVER['SCRIPT_NAME']."/".$url;
		}
		foreach ($get as $key => $value)
		{
			#if ($key == GET_SITE) continue;
			$url .= ($first ? "" : "&") .$key."=".$value;
			$first = false;
		}
		return $url;
	}

	/** @brief print a html a tag
	 * Only print tag, if url exists.
	 * @param $name Human readable Name <a>Name</a>
	 * @param $url url for a tag. Equals name if null.
	 * @param $get associative array for get parameters.
	 * @param $param extended parameters for tag <a $param>
	 */
	public function a($name, $url=null, $get=array(), $param=null)
	{
		if ($url===null) $url = $name;
		if (!$this->URLexists($url)) return false;
		print '<a href="'.$this->getURL($url, $get).'" '.$param.'>'.$name.'</a>';
		return true;
	}

	/** @brief print url @see getUrl() */
	public function url($url=null, $get=array())
	{
		print $this->getURL($url, $get);
	}

	/** @brief get current host
	 * If $_SERVER['HTTP_HOST'] not in $Brain->getData("allowed-hosts"),
	 * use first element of allowed-hosts. Port is part of HTTP_HOST.
	 * @param $primary return always first allowed hosts.
	 */
	public function getHost($primary=false)
	{
		global $Brain;
		$hosts = $Brain->getData("allowed-hosts");
		$host = $_SERVER['HTTP_HOST'];
		if ($primary) return $hosts[0];
		if (in_array($host, $hosts)) return $host;
		return $hosts[0];
	}

	/** @brief Get root URL of server. Port is part of HTTP_HOST.
	 * Example: https://www.example.com
	 * @param $primary return always first allowed hosts.
	 * @param $force_https always return https url
	 */
	public function getRootURL($primary=false, $force_https=false)
	{
#		var_dump($_SERVER); exit(1);
#		$port = $_SERVER['SERVER_PORT'];
		$https = false;
		if (
		( # Server is doing https
			isset($_SERVER['HTTPS']) &&
			!empty($_SERVER['HTTPS']) &&
			$_SERVER['HTTPS'] != "off"
		) || ( # remote proxy is doing https
			isset ($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			$_SERVER['HTTP_X_FORWARDED_PROTO'] == "https"
		) || ( # we force https
			$force_https
		)) $https = true;
/*		$_SERVER['HTTPS'] != "off") {
			$https = true;
			if ($port == 443) $port = "";
		} else {
			if ($port == 80) $port = "";
		}
		if ($port != "") $port = ":".$port;*/
		$url = "http";
		if ($https) $url .= "s";
		$url .= "://";
		$url .= $this->getHost($primary);
#		$url .= $port;
		return $url;
	}

	/** @brief 301 redirect for relative URL */
	public function permRedirectRelative($url)
	{
		$this->permRedirect($this->getRootURL().$url);
	}

	/** @brief 301 redirect for absolute URL 
	 * @note exit(0) is called. Clean up before doing redirect! 
	 */
	public function permRedirect($url)
	{
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".$url);
		exit(0);
	}

	/** @brief 302 redirect for relative URL */
	public function tempRedirectRelative($url)
	{
		$this->tempRedirect($this->getRootURL().$url);
	}

	/** @brief 302 redirect for absolute URL
	 * @note exit(0) is called. Clean up before doing redirect!
	 */
	public function tempRedirect($url)
	{
		header("HTTP/1.1 302 Moved Temporarily");
		header("Location: ".$url);
		exit(0);
	}

	/** @brief return href for canonical header
	 * This always uses the first allowed host and https
	 */
	public function canonicalHeadHref()
	{
		global $Brain;
		$site = $Brain->getData('req-url');
		return $this->getRootURL(true, true).$this->urld->canonicalHeadURL($site);
	}

	/** @brief sets 'canonical' header. Use before any print or echo */
	public function setCanonicalHeader()
	{
		header('Link: <'.$this->canonicalHeadHref().'>; rel="canonical"');
	}
}
