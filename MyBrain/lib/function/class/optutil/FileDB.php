<?php
/**
 * @name FileDB.php
 * Textfile based Database
 * The resource locator is used to get or set values, which are
 * stored into textfiles.
 * @Example:
 * $fdb = new FileDB('/var/exampledb');
 * $fdb->saveValue(array("user","email"),"test@example.com");
 * # The line above will result in the following:
 * # /var/exampledb/user/email.txt
 * # Objects are getting saved JSON encoded.
 * @uses TextFile.php for handling text files.
 * Dev-start: 9.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.3
 */
class FileDB implements IBasicDatabase
{
	/**
	 * Contains the root path of this database.
	 * @var string $root
	 */
	private $root = null;
	
	/**
	 * Contains the default file ending used for value files.
	 * @var string $file_ending
	 */
	private $file_ending = '.txt';
	
	/**
	 * New textfile based Database.
	 * @param string $root: Path to root folder of database. Without trailing slash.
	 * @param string $file_ending: Sets the default ending for textfiles. (Default: .txt)
	 */
	public function __construct($root, $file_ending = '.txt')
	{
		$this->root = $root;
		$this->file_ending = $file_ending;
	}
	
	/**
	 * Get value by resource locator.
	 * @see BasicDatabase::getValue()
	 */
	public function getValue($resource_locator)
	{
		$file = new TextFile($this->getFileNameByRl($resource_locator));
		return $file->read() !== false ? $file->getContent() : false;
	}
	
	/**
	 * Get value by resource locator as array.
	 * @see BasicDatabase::getValueAsArray()
	 */
	public function getValueAsArray($resource_locator)
	{
		$file = new TextFile($this->getFileNameByRl($resource_locator));
		return $file->read() !== false ? $file->getContentAsArray() : false;
	}
	
	/**
	 * Get object by resource locator.
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
	 * Save value for resource locator.
	 * @see BasicDatabase::setValue()
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
	 * @see BasicDatabase::saveValueFromArray()
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
	 * Save object by resource locator.
	 * @see BasicDatabase::saveObject()
	 */
	public function saveObject($resource_locator, $object)
	{
		return $this->saveValue($resource_locator, json_encode($object));
	}
	
	/**
	 * Returns a new FileDB with given resource locator as root.
	 * @see BasicDatabase::chroot()
	 */
	public function chroot($resource_locator)
	{
		$new_root = $this->getSubPathByRl($resource_locator);
		return new FileDB($new_root);
	}
	
	/**
	 * Returns root path from the database.
	 * @return string $root
	 * @example
	 * $newDB = new FileDB('/var/exampledb');
	 * echo $newDB->getRoot(); // /var/exampledb
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
	 * @param array $resource_locator
	 * @return boolean $success
	 */
	public function createNodes($resource_locator)
	{
		foreach($resource_locator as $i => $node)
		{
			if($i == count($resource_locator) - 1)
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
	 * @param array $resource_locator
	 * @return string $filename
	 */
	public function getFileNameByRl($resource_locator)
	{
		return $this->getSubPathByRl($resource_locator).$this->file_ending;
	}
	
	/**
	 * Returns sub path (on filesystem) for the node $node.
	 * @param array $resource_locator
	 * @param int $node: last included part of $resource_locator. (counting from 0)
	 * @return string $path: Path on filesystem for this resource locator. Full path, if
	 * $node omitted or < 0.
	 * 
	 * @example
	 * $root = /var/exampledb
	 * $ex = $this->getSubPathByRl(array("path","to","value", 1);
	 * echo $ex; // /var/exampledb/path/to
	 */
	public function getSubPathByRl($resource_locator, $node = -1)
	{
		return $this->root.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$this->getSubRl($resource_locator,$node));
	}
	
	/**
	 * Returns sub resource locator as array.
	 * @param array $resource_locator
	 * @param int $node
	 * @return array $sub_rl:
	 */
	public function getSubRl($resource_locator, $node = -1)
	{
		if($node < 0)
		{
			return $resource_locator;
		}
		else
		{
			$last_part = count($resource_locator) - 1;
			$needed_parts = array();
			for($i = 0; $i <= $last_part; $i++)
			{
				if($i <= $node && $i <= $last_part)
				{
					array_push($needed_parts, $resource_locator[$i]);
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
