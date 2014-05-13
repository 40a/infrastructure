<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 28.04.11
 * Time: 00:15
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_IO_File {
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var integer
	 */
	protected $size;

	/**
	 * @var integer
	 */
	protected $modificationTime;

	/**
	 * @var boolean
	 */
	protected $executable;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var TYPO3_Extension_IO_Directory
	 */
	protected $directory;

	/**
	 * @var boolean
	 */
	protected $modified = FALSE;

	/**
	 * @var string
	 */
	protected $hash;

	/**
	 * @var array
	 */
	protected $writeCallback;

	/**
	 * @param string $file
	 * @return TYPO3_Extension_IO_File
	 */
	public static function read($file) {
		if (!file_exists($file)) {
			throw new RuntimeException('File "' . $file . '" not found');
		}

		$content = file_get_contents($file);

		return new TYPO3_Extension_IO_File(
			basename($file),
			filesize($file),
			filemtime($file),
			is_executable($file),
			$content,
			self::createHash($content)
		);
	}

	public static function createEmpty($name) {
		return new TYPO3_Extension_IO_File($name, 0, 0, FALSE, '');
	}

	public static function createHash($content) {
		return substr(md5($content), 0, 4);
	}

	public function __construct($name, $size, $modificationTime, $executable, $content, $hash = NULL) {
		$this->setName($name);
		$this->setSize($size);
		$this->setModificationTime($modificationTime);
		$this->setExecutable($executable);
		$this->setContent($content);
		$this->setHash($hash);

		$this->modified = FALSE;
	}

	public function __toArray() {
		
	}

	public function __toTree($depth) {
		echo str_repeat(' ', ($depth + 1) * 2) . $this->getName() . PHP_EOL;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setSize($size) {
		$this->size = (int) $size;
	}

	public function getSize() {
		return $this->size;
	}

	public function setModificationTime($modificationTime) {
		$this->modificationTime = (int) $modificationTime;
	}

	public function getModificationTime() {
		return $this->modificationTime;
	}

	public function setExecutable($executable) {
		$this->executable = (bool) $executable;
	}

	public function getExecutable() {
		return $this->executable;
	}

	public function setContent($content) {
		if (isset($this->content)) {
			$this->modified = TRUE;
			$this->setSize(strlen($content));
			$this->setHash(self::createHash($content));
		}

		$this->content = $content;
	}

	public function getContent() {
		return $this->content;
	}

	public function setHash($hash) {
		$this->hash = $hash;
	}

	public function getHash() {
		return $this->hash;
	}

	public function getShortHash() {
		return substr($this->getHash(), 0, 4);
	}

	public function setDirectory(TYPO3_Extension_IO_Directory $directory) {
		$this->directory = $directory;
	}

	public function getDirectory() {
		return $this->directory;
	}

	public function isModified() {
		return $this->modified;
	}

	public function writeTo($directory) {
		$this->executeWriteCallback();

		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$selfFileName = $directory . $this->getName();

		file_put_contents($selfFileName, $this->getContent());

		// Preserve modification time if content was not changed
		if ($this->isModified() === FALSE) {
			touch($selfFileName, $this->getModificationTime());
		}
	}

	public function write() {
		if (is_null($this->directory)) {
			throw new RuntimeException('This is an anonymous file and not related to a directory.');
		}

		$this->writeTo($this->getDirectory()->getFullPath());
	}

	public function getFullPath() {
		if (is_null($this->directory)) {
			throw new RuntimeException('This is an anonymous file and not related to a directory.');
		}

		return $this->getDirectory()->getFullPath() . $this->getName();
	}

	public function getRelativePath() {
		if (is_null($this->directory)) {
			throw new RuntimeException('This is an anonymous file and not related to a directory.');
		}

		return $this->getDirectory()->getRelativePath() . $this->getName();
	}

	public function setWriteCallback($writeCallbackObject, $writeCallbackMethod) {
		$this->writeCallback = array($writeCallbackObject, $writeCallbackMethod);
	}

	protected function executeWriteCallback() {
		if (isset($this->writeCallback)) {
			call_user_func_array(
				$this->writeCallback,
				array($this)
			);
		}
	}
}
