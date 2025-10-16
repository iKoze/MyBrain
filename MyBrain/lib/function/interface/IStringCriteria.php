<?php
/**
 * @name IStringCriteria.php
 * Interface for string checking.
 * Dev-start: 28.12.2013.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
interface IStringCriteria
{
	/**
	 * Check string whether it matches a criterium
	 * @param string $input_string
	 * @return boolean $matches: true if string matches
	 */
	public function checkString($input_string);
}
