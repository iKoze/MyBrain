<?php
/**
 * @name TextFile.php
 * Class for hanlding textfiles.
 * Dev-start: 2.12.2012.
 * @author Florian Schiessl <florian@floriware.de>
 * @version 0.1
 */
class TextFile
{
	/**
	 * Contains the full path of the file
	 * @var string $full_path
	 */
	protected $full_path = null;
	
	/**
	 * The content of file $full_path linewise.
	 * @var array $content
	 */
	protected $content = array();
	
	/**
	 * If there are any unsaved changes to file.
	 * @var boolean $is_saved
	 */
	protected $is_saved = false;
	
	/**
	 * New File.
	 * @param string $full_path:
	 * Path to file.
	 * @param boolean $read:
	 * Read the file or do it manually later. (Default: later)
	 */
	public function __construct($full_path = null, $read = false)
	{
		$this->setFullPath($full_path);
		if($read == true)
		{
			$this->read();
		}
	}
	
	/**
	 * Returns only the name of the file
	 * @return string $file_name
	 */
	public function getName()
	{
		return basename($this->full_path);
	}
	
	/**
	 * Sets the name of file.
	 * Folder remains unchanged.
	 * @param string $new_name
	 */
	public function setName($new_name)
	{
		$this->full_path = $this->getFolder().DIRECTORY_SEPARATOR.$new_name;
		$this->is_saved = false;
	}
	
	/**
	 * Get folder of file without trailing slash.
	 * @return string $path
	 */
	public function getFolder()
	{
		return dirname($this->full_path);
	}
	
	/**
	 * Sets the folder of file. Without trailing slash.
	 * Name remains unchanged.
	 * @param string $new_folder
	 */
	public function setFolder($new_folder)
	{
		$new_folder = rtrim($new_folder,DIRECTORY_SEPARATOR);
		$this->full_path = $new_folder.DIRECTORY_SEPARATOR.$this->getName();
		$this->is_saved = false;
	}
	
	/**
	 * Returns full path of file.
	 * Null if not initialized.
	 * @return string $full_path
	 */
	public function getFullPath()
	{
		return $this->full_path;
	}
	
	/**
	 * Sets the full path of file.
	 * @param string $new_path
	 */
	public function setFullPath($new_path)
	{
		$this->full_path = $new_path;
		$this->is_saved = false;
	}
	
	/**
	 * Read Content from File
	 * @return boolean $success
	 */
	public function read()
	{
		//TODO: Allow only reading local Files
		$file_content = file($this->full_path);
		if($file_content !== false)
		{
			$this->content = $file_content;
			return true;
		}
		return false;
	}
	
	/**
	 * Write content to File
	 * @return boolean $success
	 */
	public function write()
	{
		if($this->content != null)
		{
			if(file_put_contents($this->full_path, $this->content) !== false)
			{
				$this->is_saved = true;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Alias for write()
	 */
	public function save()
	{
		$this->write();
	}
	
	/**
	 * Returns file's content as array.
	 * @return array $content
	 */
	public function getContentAsArray()
	{
		return $this->content;
	}
	
	/**
	 * Sets content from Array.
	 * @param array $new_content
	 */
	public function setContentFromArray(array $new_content)
	{
		$this->content = $new_content;
		$this->is_saved = false;
	}
	
	/**
	 * Returns file's content as string.
	 * Line-delimiter: \n
	 * @return string $content
	 */
	public function getContent()
	{
		return implode("\n", $this->content);
	}
	
	/**
	 * Sets file's content from string.
	 * Line-delimiter: \n
	 * @param string $new_content
	 */
	public function setContent($new_content)
	{
		if($new_content != null)
		{
			$this->content = explode("\n", $new_content);
			$this->is_saved = false;
		}
		else
		{
			$this->content = array();
			$this->is_saved = false;
		}
	}
	
	/**
	 * Returns whether file exists or not.
	 * @return boolean $file_exists
	 */
	public function exists()
	{
		return file_exists($this->full_path);
	}
	
	/**
	 * Return whether latest changes are saved to file or not.
	 * @return boolean $is_saved
	 */
	public function isSaved()
	{
		return $this->is_saved;
	}
}