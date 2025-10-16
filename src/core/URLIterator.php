<?php
/** @brief MyBrain URL Iterator
 * @author Florian Schiessl <florian@floriware.de> (6.11.2015)
 * @see https://secure.php.net/manual/de/class.iterator.php
 *
 * Implements php's Iterator so you easily can use it.
 *
 * Example
 * =======
 * ~~~~~{.php}
 * foreach(URLIterator::iter("/this/is/a/example") as $rem => $sub) {
 *   print "$sub .. $rem"
 * }
 * ~~~~~
 *
 *     Results:
 *     this/is/a/example .. 
 *     this/is/a .. example
 *     this/is .. a/example
 *     this .. is/a/example
 *
 * @note it's $rem => $sub so that you're able to use $sub standalone.
 *
 * ~~~~~{.php}
 * foreach(URLIterator::iter("/this/is/a/example") as $sub) {
 *   print "$sub\n";
 * }
 * ~~~~~
 *
 *     Results:
 *     this/is/a/example
 *     this/is/a
 *     this/is
 *     this
 */
class URLIterator implements Iterator
{
	public $url = "";

	private $pos = 0;
	private $pre = "";
	private $post = "";
	private $split = "";

	/**
	 * @brief new URLIterator
	 * @param string $url: url to iterate over
	 * @param character $split: char to use as separator
	 */
	public function __construct($url, $split = "/")
	{
		$this->split = $split;
		$this->url = trim($url, $this->split);
	}

	public function rewind()
	{
		$this->pos = strlen($this->url);
		$this->pre = $this->url;
		$this->post = "";
	}	

	public function next()
	{
		$this->pos = strrpos($this->pre, $this->split);
		$this->pre = substr($this->url, 0, $this->pos);
		$this->post = substr($this->url, $this->pos + 1, strlen($this->url));
	}

	public function current()
	{
		return $this->pre;
	}

	public function key()
	{
		return $this->post;
	}

	public function valid()
	{
		return $this->pos !== false;
	}

	/** @brief returns URLIterator instance for $url */
	public static function iter($url)
	{
		return new self($url);
	}
}

	
