<?php
/**
 * @brief HTML template provider
 * @author Florian Schießl <florian@floriware.de> (8.11.2016)
 *
 * Mode of operation
 * =================
 * @see Templater
 *
 * makes HTML content usable as DOMDocument. Use $this->meta() for meta tags
 * in your template.
 */
namespace Floriware\MyBrain\Core;

use DOMDocument;
use DOMNode;
use Floriware\MyBrain\Core\Templater;

class HTMLTemplater
extends Templater
{
	/** @brief meta tags found by findMetas(). Stored by pattern $meta[name]=content */
	public $metas = array();

	/** @brief all meta names, already printed by meta() */
	public $used_metas = array();

	# http://stackoverflow.com/questions/2087103/how-to-get-innerhtml-of-domnode
	/** @brief gets HTML-code of all inner DOM nodes */
	function DOMinnerHTML(DOMNode $element) 
	{ 
		$innerHTML = ""; 
		$children  = $element->childNodes;

		foreach ($children as $child) 
		{ 
			$innerHTML .= $element->ownerDocument->saveHTML($child);
		}

		return $innerHTML; 
	} 

	/** Parse $this->content as DOMDocument. Extract title.
	 * @retval $this->DOMdoc whole DOMDocument()
	 * @retval $this->DOMhead <head>
	 * @retval $this->title <title>
	 *
	 * Values are not getting returned, they're stored locally for usage
	 * within template.
	 * @see Templater::render()
	 */
	public function render()
	{
		$this->DOMdoc = $doc = new DOMDocument();
		$this->title = "";
		$doc->loadHTML($this->content);
		$this->DOMhead = $doc->getElementsByTagName("head")->item(0);
		if ($this->DOMhead !== null) {
			$this->DOMtitle = $this->DOMhead->getElementsByTagName("title")->item(0);
			if ($this->DOMtitle !== null) {
				$this->title = $this->DOMinnerHTML($this->DOMtitle);
			}
		}
		$this->body = $this->DOMinnerHTML(
			$doc->getElementsByTagName("body")->item(0)
		);
		$this->findMetas();
		parent::render();
	}

	/** Find <meta name= like meta tags and store content= in array with name as key
	 * @retval $this->metas
	 */
	public function findMetas()
	{
		$this->metas = array();
		if ($this->DOMhead == null) return;
		foreach ($this->DOMhead->getElementsByTagName("meta") as $meta) {
			$name = $meta->getAttribute("name");
			if ($name == null) continue;
			$content = $meta->getAttribute("content");
			$this->metas[$name] = $content;
		}
	}

	/** Print a user overridable meta tag.
	 * Use params as default. If equivalent meta tags were found by
	 * findMetas() within content of template, they'll be used instead.
	 * @param $name
	 * @param $content
	 */
	public function meta($name, $content)
	{
		array_push($this->used_metas, $name);
		if (array_key_exists($name, $this->metas))
		$content = $this->metas[$name];
		$this->printMeta($name, $content);
	}

	/** @brief prints all metas which weren't printed by meta() */
	public function extraMetas()
	{
		foreach ($this->metas as $name => $content)
		{
			if (in_array($name, $this->used_metas)) continue;
			$this->printMeta($name, $content);
		}
	}

	/** @brief print meta string */
	public function printMeta($name, $content)
	{
		print '<meta name="'.$name.'" content="'.$content.'">'."\n";
	}
}

