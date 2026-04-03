<?php
/** @brief Redirects URLs to other URLDirectors
 * @author Florian Schiessl <florian@floriware.de> (1.11.2015)
 *
 * Mode of operation
 * =================
 * The MyBrain URLDirector is built to get chained for great flexibility. Each
 * URLDirector can point to another URLDirector which can point to yet another.
 * @warning Make sure you don't setup a loop. Eg. a URLDirector pointing to
 * another which is pointing back to the first. This would cause a endless
 * recursion!
 *
 * Pointing is done by calling registerChild(). It takes a relative URL and
 * another URLDirector which the relative URL points to. Hereby a URLDirector
 * get's bound to this URL.
 *
 * Examples
 * --------
 * Let's assume we've got 4 URLDirector instances:
 * -# $root
 * -# $examples
 * -# $videos
 * -# $pictures
 *
 * Setup:
 *
 * ~~~~~{.php}
 *     $root = new URLDirector();
 *     $examples = new FileControllerDeliverer();
 *     $videos = new FileControllerDeliverer();
 *     $pictures = new FileControllerDeliverer();
 *     $root->registerChild("", $pictures)
 *     $root->registerChild("examples", $examples)
 *     $root->registerChild("videos", $videos)
 *     $root->registerChild("examples/videos", $videos)
 *     $examples->registerChild("pictures", $pictures)
 * ~~~~~
 * This results in the following available URLs:
 * * /
 * * /examples
 * * /videos
 * * /examples/videos
 * * /examples/pictures
 *
 * What will happen now?
 * ---------------------
 * All urls must initially be passed to the root URLDirector. Each URLDirector
 * searches the **deepest** path it can provide. If you pass $root 
 *
 *     /
 *
 * By calling $root->getURL("/")
 * This is what happens:
 * -# $root calls $pictures with "" as remainder.
 *
 * If you pass:
 *
 *     /examples/audio/music
 *
 * This is what happens:
 * -# $root calls $examples with "audio/music" as remainder.
 *
 * If you pass:
 *
 *     /examples/videos
 *
 * -# $root calls $videos with "" as remainder
 *
 * If you pass:
 *
 *     /examples/pictures
 *
 * -# $root calls $examples with "pictures" as remainder.
 * -# $examples calls pictures with "" as remainder.
 *
 * If you pass:
 *
 *     /fun
 *
 * -# $root calls $pictures with "fun" as remainder.
 * -# $pictures probably doesn't know "fun" and returns null. :'(
 *
 * And now?
 * --------
 * That's up to the object the URLDirector directed to. It could return a
 * controller for example.
 * 
 */
namespace Floriware\MyBrain\Core;

use Floriware\MyBrain\Interface\iURLDirector;

class URLDirector implements iURLDirector
{
	/**
	 * @brief Uses relativeurls as keys and stores their assigned iURLDirector.
	 */
	public $ns;

	/**
	 * @brief contains URL associated data.
	 * @see registerURLData()
	 */
	public $urld;

	/**
	 * @brief new URLDirector
	 * @param array $ns: "relativeurl" => URLDirector
	 */
	public function __construct($ns = array())
	{
		$this->ns = $ns;
		$this->urld = array();
	}
	
	/**
	 * @brief Find matching URLDirector and return it's result.
	 *
	 * Use URLIterator iter() to iterate over given $relativeurl.
	 * Find longest occurance of $sub in $ns and return it's getURL().
	 * Otherwise check whether root element exists ($ns[""]) and return it's 
	 * getURL().
	 * Else return null.
	 */
	public function getURL($relativeurl)
	{
		$relativeurl = $this->clearifyURL($relativeurl);
		foreach (URLIterator::iter($relativeurl) as $rem => $sub)
		{
			if (array_key_exists($sub, $this->ns))
			return $this->ns[$sub]->getURL($rem);
		}
		if (array_key_exists("", $this->ns))
		return $this->ns[""]->getURL($relativeurl);
		return null;
	}

	/**
	 * @brief Assign a child URLDirector to a relativeurl
	 */
	public function registerChild($relativeurl, iURLDirector $child)
	{
		$this->ns[$relativeurl] = $child;
		return true;
	}

	public function getAllSubURLs()
	{
		$res = array();
		ksort($this->ns);
		foreach ($this->ns as $key => $item)
		{
			$res[$key] = array();
			$res[$key][0] = $this->getURL($key);
			$res[$key][1] = $item->getAllSubURLs();
		}
		return $res;
	}

	/**
	 * @brief get previous assigned data from relative url
	 * @see registerURLData()
	 */
	public function getURLData($relativeurl, $dict="")
	{
		foreach (URLIterator::iter($relativeurl) as $sub)
		{
			if (array_key_exists($dict, $this->urld) && 
				array_key_exists($sub, $this->urld[$dict]))
			return $this->urld[$dict][$sub];
		}
		return null;
	}

	/**
	 * @brief add some data to a relative url
	 *
	 * This is useful in child classes if you'd like to override a default
	 * controller, for example. Use $dict if you prefer to have a own
	 * namespace.
	 * @param string $relatieurl
	 * @param mixed $data
	 * @param string $dict: Namespace selector
	 */
	public function registerURLData($relativeurl, $data, $dict="")
	{
		if (!array_key_exists($dict, $this->urld)) $this->urld[$dict] = array();
		$this->urld[$dict][$relativeurl] = $data;
	}

	/** @brief remove unused characters from URL, lowercase it. */
	public function clearifyURL($url)
	{
		# we dislike case sensitivity
		$url = strtolower(strtok(strtok($url, "?"), "#"));
		$url = trim($url, "/");
		return $url;
	}

	/** @brief return the canonical redirect URL for a site.
	 *
	 * The same like canonicalHeadURL() but with get parameters.
	 * @see canonicalHeadURL()
	 */
	public function canonicalRedirectURL($site)
	{
		$site = $this->canonicalHeadURL($site);
		$get = $_GET;
		$gets = "";
		foreach ($get as $key => $value)
		{
			$gets .= "&".$key."=".$value;
		}
		if ($gets != "") $gets = "?".ltrim($gets,"&"); # replace first '&' with '?'
		return $site.$gets;
	}

	/** @brief return the canonical url for http and html header
	 *
	 * This is the canonical URL you want to use for 301 redirects.
	 * It lowercases the URL and adds a trailing slash.
	 * Canonical head urls are always without GET parameters. GET Parameters shall
	 * only be used for content arranging like ordering or paging. Use suburls for
	 * other content.
	 * @see canonicalRedirectURL()
	 */
	public function canonicalHeadURL($site)
	{
		$site = $this->clearifyURL($site);
		if ($site != "") $site = "/".$site; # avoid double slash on root
		return $site."/";
	}

}
