<?php
/** @brief MyBrain Controller File Deliverer
 * @author Florian Schiessl <florian@floriware.de (1.11.2015)
 */
namespace Floriware\MyBrain\Core;


class FileControllerDeliverer extends URLDirector
 {
 	public $fsroot;
	public $ftype;
	public $controller;

	/**
	 * @brief return $controller for $ftype files in $fsroot.
	 */
 	public function __construct($fsroot, $ftype, $controller)
 	{
		parent::__construct();
		$this->fsroot = $fsroot;
		$this->ftype = $ftype;
		$this->controller = $controller;
		// TODO fs check dir exists
		if (!is_dir($this->fsroot)) {
			echo "Path '$this->fsroot' does not exist!";
			exit(0);
		}
	}

	/**
	 * Use URLIterator iter() to iterate over given $relativeurl.
	 * 1. Assume $sub is suffix for $fsroot => $name
	 * 2. Check whether dirname($name) is a valid directory.
	 * 3. Check whether index file exists. If, return it's controller.
	 * 4. Check whether $name is file of $ftype. If, return it's controller
	 */
	public function getURL($relativeurl)
	{
		$res = parent::getURL($relativeurl);
		if ($res !== null) return $res;
		foreach (URLIterator::iter($relativeurl) as $rem => $sub)
		{
			#print "$sub | $rem\n";
			$name = $this->fsroot."/".$sub;
			$dir = dirname($name);
			if (!is_dir($dir) && $dir != $this->fsroot) continue;
			#print "is dir:".dirname($name)."\n";
			$index = $name . "/index" . $this->ftype;
			#print $index;
			if (is_file($index)) return $this->getController($sub, $index, $rem);
			$file = $name . $this->ftype;
			if (is_file($file)) return $this->getController($sub, $file, $rem);
			#print "next\n";
		}
		$index = $this->fsroot."/index".$this->ftype;
		if (is_file($index)) return $this->getController("", $index, $relativeurl);
		return null;
	}

	/** @brief return controller for $file and call getURL for $relativeurl
	 *if it implements iURLDirector 
	 */
	private function getController($sub, $file, $relativeurl)
	{
		$cname = $this->getURLData($sub, "controller");
		if ($cname === null) $cname = $this->controller;
		$controller = new $cname($this, $file);
		if ($controller instanceof iURLDirector)
		return $controller->getURL($relativeurl);
		if ($relativeurl != "") return null;
		return $controller;
	}

	public function registerSubController($relativeurl, $controller)
	{
		$this->registerURLData($relativeurl, $controller, "controller");
	}

	public function getAllSubURLs()
	{
		return $this->iterDir();
	}

	private function iterDir($dir="")
	{
		$curdir = $this->fsroot."/".$dir;
		$res = array();
		if ($handle = opendir($curdir))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry == "." || $entry == "..") continue;
				if ($entry == "index".$this->ftype) continue;
				if (startsWith($entry, "#")) continue;
				if (!endsWith($entry, $this->ftype) && !is_dir($curdir."/".$entry)) continue;
				$entry = basename($entry, $this->ftype);
		#		print "E: $entry\n";
				$res[$entry] = array();
				$res[$entry][0] = $this->getURL($dir."/".$entry);
				$res[$entry][1] = array();
				if (is_dir($curdir."/".$entry))
				$res[$entry][1] = $this->iterDir($dir."/".$entry);
			}
		}
		ksort($res);
		return $res;
	}
}
