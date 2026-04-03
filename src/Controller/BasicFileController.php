<?php
namespace Floriware\MyBrain\Controller;

use Floriware\MyBrain\Interface\iURLDirector;
use Floriware\MyBrain\Interface\iTemplateUser;

class BasicFileController
extends BasicController 
implements iURLDirector, iTemplateUser
{
	public $urld;
	public $file;
	public $view = null;
	public $relativeurl;

	public function __construct($urldirector, $file)
	{
		$this->urld = $urldirector;
		$this->file = $file;
	}

	public function run()
	{
		return;
	}

	public function getURL($url)
	{
		$this->relativeurl = $url;
		if ($url != "")
		return null;
		return $this;
	}

	public function getAllSubURLs()
	{
		return array();
	}

	public function getSiteName()
	{
		return basename($this->file, $this->urld->ftype);
	}

	public function getContent()
	{
		$job = "require";
		$content = array();
		$file = $this->getViewPath();
		return array($job, $content, $file);
	}

	public function getViewPath()
	{
		if ($this->view !== null)
		return dirname($this->file)."/#".$this->getSiteName()."/".$this->view.$this->urld->ftype;
		return $this->file;
	}
}
