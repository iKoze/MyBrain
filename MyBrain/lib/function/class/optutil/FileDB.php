<?php
/**
 * @name FileDB.php
 * Textfile based Database
 * @uses TextFile.php for handling text files.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.2
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
		//TODO add a function to validate the root. Like 'is existent' or 'has access'
	}
	
	/**
	 * Get value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @return string $value: on success. || boolean false: on error.
	 * @see BasicDatabase::getValue()
	 * @example
	 * $ex = getValue('path.to.value');
	 * echo $ex; // 'this is a test' (See setValue())
	 */
	public function getValue($resource_locator)
	{
		$file = new TextFile($this->getFileNameByRl($resource_locator));
		return $file->read() !== false ? $file->getContent() : false;
	}
	
	/**
	 * Get value by resource locator as array.
	 * One element/line in value
	 * @see BasicDatabase::getValueAsArray()
	 */
	public function getValueAsArray($resource_locator)
	{
		$file = new TextFile($this->getFileNameByRl($resource_locator));
		return $file->read() !== false ? $file->getContentAsArray() : false;
	}
	
	/**
	 * Get object by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @return mixed $value: on success. || boolean false: on error.
	 * @see BasicDatabase::getObject()
	 */
	public function getObject($resource_locator)
	{
		$result = $this->getValue($resource_locator);
		if($result !== false)
		{
			return json_decode($result);
		}
		return false;
	}
	
	/**
	 * Save value by resource locator.
	 * @param string $resource_locator: Resource locator string.
	 * @param string $value: Set field to $value.
	 * @return boolean $success: True on success, false on error.
	 * @see BasicDatabase::setValue()
	 * @example setValue('path.to.value', 'this is a test');
	 */
	public function saveValue($resource_locator, $value)
	{
		if($this->createNodes($resource_locator))
		{
			$filename = $this->getFileNameByRl($resource_locator);
			$file = new TextFile($filename);
			$file->setContent($value);
			return $file->write();
		}
		return false;
	}
	
	/**
	 * Save values from array by resource locator.
	 * One element/line in value. Using newline (\n) in array values
	 * WILL lead to newlines in value. Use saveObject() in order to
	 * store Arrays.
	 * @see BasicDatabase::saveValueFromArray()
	 * @param string $resource_locator
	 * @param string $value_array
	 * @return boolean success
	 */
	public function saveValueFromArray($resource_locator, $value_array)
	{
		if($this->createNodes($resource_locator))
		{
			$filename = $this->getFileNameByRl($resource_locator);
			$file = new TextFile($filename);
			$file->setContentFromArray($value_array);
			return $file->write();
		}
		return false;
	}
	
	/**
	 * Save object by resource locator. Object will be saved as JSON in File of resource Locator.
	 * @param string $resource_locator: Resource locator string.
	 * @param mixed $object: Object to save.
	 * @see BasicDatabase::saveObject()
	 */
	public function saveObject($resource_locator, $object)
	{
		return $this->saveValue($resource_locator, json_encode($object));
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
	 * Returns a new FileDB with given resource locator as root.
	 * @see BasicDatabase::chroot()
	 * @return FileDB $chrooted_filedb
	 */
	public function chroot($resource_locator)
	{
		$new_root = $this->getSubPathByRl($resource_locator);
		return new FileDB($new_root);
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
	 * Returns default file ending for textfiles.
	 * @return string $file_ending: (Default: .txt)
	 */
	public function getFileEnding()
	{
		return $this->file_ending;
	}
	
	/**
	 * Creates sub folders needed for storing a file (if needed)
	 * @param string $resource_locator
	 * @return boolean $success
	 */
	public function createNodes($resource_locator)
	{
		$rl_array = $this->getSubRlArray($resource_locator);
		foreach($rl_array as $i => $node)
		{
			if($i == count($rl_array) - 1)
			{
				// last node
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
	 * Returns the filename matching given resource locator.
	 * @param string $resource_locator
	 * @return string $filename
	 */
	public function getFileNameByRl($resource_locator)
	{
		return $this->getSubPathByRl($resource_locator).$this->file_ending;
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