<?php
/** @brief MyBrain URL Director Interface
 * @author Florian Schiessl <florian@floriware.de> (1.11.2015)
 * @see URLDirector
 */
namespace Floriware\MyBrain\Interface;

interface iURLDirector
{
	/** @brief try to get result for $relativeurl
	 * If the result implements iURLDirector, return the result of it's getURL
	 * called with the remainder of the url. (delegation)
	 * @see URLDirector
	 * @see URLIterator
	 * @retval object $result: if url results in something
	 * @retval null: url does not exist
	 */
	public function getURL($relativeurl);

	/**
	 * @brief return all URLs and associated objects this URLDirector has 
	 * registered. (recursion)
	 *
	 * Return only actually existing URLs i.e. only URLs of which getURL()
	 * wouldn't return null. If a associated object implements 
	 * iURLDirector, add the result of it, too. If the result would be too 
	 * large, it's up to you whether you'd like to return an empty array 
	 * instead.
	 * 
	 * Format:
	 * ~~~~~{.php}
	 *      $res = array(
	 *        "suburl" => array(
	 *          0 => Object,
	 *          1 => array()
	 *         ),
	 *       );
	 * ~~~~~
	 * $res['suburl'][0] is the result of $this->getUrl('suburl'). May be null.
	 *
	 * $res['suburl'][1] is the result of the assigned URLDirector's
	 * getAllSubURLs()
	 *
	 */
	public function getAllSubURLs();
}
