<?php
/**
 * @name FileDB.php
 * Textfile based Database
 * @uses TextFile.php for handling text files.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class FileDB implements BasicDatabase
{
	/**
	 * Contains the root folder of this database.
	 * @var string $root
	 */
	private $root = null;
	
	/**
	 * Contains default separator for resource locator paths.
	 * @var string $sep
	 */
	private $sep = '.';
	
	/**
	 * Contains default file ending for textfiles.
	 * @var string $file_ending
	 */
	private $file_ending = '.txt';
	
	/**
	 * New textfile based Database.
	 * @param string $root: Path to root folder of database. Without trailing slash. (See getRoot())
	 * @param string $file_ending: Sets the default ending for textfiles. (Default: .txt)
	 * @param string $rl_separator: Sets the default resource locator separator. (Default: .)
	 */
	public function __construct($root, $file_ending = '.txt', $rl_separator = '.')
	{
		$this->root = $root;
		$this->file_ending = $file_ending;
		$this->sep = $rl_separator;
	}
	
	/**
	 * Get value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @return string $file_content: on success. || boolean false: on error.
	 * @see BasicDatabase::getValue()
	 * @example
	 * $ex = getValue('path.to.value');
	 * echo $ex; // 'this is a test' (See setValue())
	 */
	public function getValue($resource_locator)
	{
		$file = new TextFile($this->getSubPathByRl($resource_locator).$this->file_ending);
		if($file->exists())
		{
			$file->read();
			return $file->getContent();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Set value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @param string $value: Set field to $value.
	 * @return boolean $success: True on success, false on error.
	 * @see BasicDatabase::setValue()
	 * @example setValue('path.to.value', 'this is a test');
	 */
	public function setValue($resource_locator, $value)
	{
		$rl_array = $this->getSubRlArray($resource_locator);
		foreach($rl_array as $i => $node)
		{
			if($i == count($rl_array) - 1)
			{
				// last node
				$filename = $this->getSubPathByRl($resource_locator).$this->file_ending;
				$file = new TextFile($filename);
				$file->setContent($value);
				$file->write();
				return true;
			}
			else
			{
				// other nodes
				$filename = $this->getSubPathByRl($resource_locator, $i);
				if(file_exists($filename))
				{
					if(!is_dir($filename))
					{
						// not ok
						return false;
					}
				}
				else
				{
					mkdir($filename);
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns root path to database.
	 * @return string $root
	 * @example
	 * $newDB = new FileDB('/var/db');
	 * echo $newDB->getRoot(); // /var/db
	 */
	public function getRoot()
	{
		return $this->root;
	}
	
	/**
	 * Returns default resource locator separator.
	 * @return string $sep: (Default: .)
	 */
	public function getSeparator()
	{
		return $this->sep;
	}
	
	/**
	 * Returns default file ending for textfiles.
	 * @return string $file_ending: (Default: .txt)
	 */
	public function getFileEnding()
	{
		return $this->file_ending;
	}
	
	/**
	 * Returns sub path (on filesystem) for the node $node.
	 * @param string $resource_locator: Resource locator string.
	 * @param int $node: last included part of $resource_locator. (counted from the 
	 * beginning, start 0)
	 * @return string $path: Path on filesystem for this resource locator. Full path, if
	 * $node omitted or < 0.
	 * 
	 * @example
	 * $root = /var/db
	 * $ex = $this->getSubPathByRl('path.to.value', 1);
	 * echo $ex; // /var/db/path/to
	 */
	public function getSubPathByRl($resource_locator, $node = -1)
	{
		return $this->root.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$this->getSubRlArray($resource_locator,$node));
	}
	
	/**
	 * Returns sub resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @param int $node: last included part of $resource_locator. (counted from the 
	 * beginning, start 0)
	 * @return string $new_resource_locator: input if $node omitted or < 0;
	 * 
	 * @example
	 * $ex = getSubRl('very.long.path.to.special.value', 2);
	 * echo $ex; // very.long.path
	 */
	public function getSubRl($resource_locator, $node = -1)
	{
		if($node < 0)
		{
			return $resource_locator;
		}
		else
		{
			$parts = $this->getSubRlArray($resource_locator, $node);
			$last_part = count($parts) - 1;
			$new_rl = '';
			for($i = 0; $i <= $last_part; $i++)
			{
				if($i < $last_part)
				{
					$new_rl .= $parts[$i];
				}
				else
				{
					return $new_rl;
				}
				$new_rl .= $this->sep;
			}
		}
		return $new_rl;
	}
	
	/**
	 * Returns sub resource locator as array. Same behaviour as getSubRl().
	 * @param string $resource_locator
	 * @param int $node
	 * @return array $parts:
	 */
	public function getSubRlArray($resource_locator, $node = -1)
	{
		$parts = explode($this->sep, $resource_locator);
		if($node < 0)
		{
			return $parts;
		}
		else
		{
			$last_part = count($parts) - 1;
			$needed_parts = array();
			for($i = 0; $i <= $last_part; $i++)
			{
				if($i <= $node && $i <= $last_part)
				{
					array_push($needed_parts, $parts[$i]);
				}
				else
				{
					return $needed_parts;
				}
			}
			return $needed_parts;
		}
	}
	
	/**
	 * Nothing special.
	 */
	public function __destruct()
	{
		return;
	}
}