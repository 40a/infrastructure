<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 28.04.11
 * Time: 00:15
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_IO_SubDirectory extends TYPO3_Extension_IO_Directory {
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var TYPO3_Extension_IO_Directory
	 */
	protected $parentDirectory;

	public function __construct($name) {
		$this->setName($name);
	}

	public function __toTree($depth = 0) {
		echo str_repeat(' ', $depth * 2) . '[' . $this->getName() . ': ' . $this->getRelativePath() . ']' . PHP_EOL;
		parent::__toTree();
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function getPath() {
		return $this->getName() . DIRECTORY_SEPARATOR;
	}

	public function setParentDirectory(TYPO3_Extension_IO_Directory $parentDirectory) {
		$this->parentDirectory = $parentDirectory;
	}

	public function getParent() {
		return $this->parentDirectory;
	}

	public function getFullPath() {
		return $this->getParent()->getFullPath() . $this->getPath();
	}

	public function getRelativePath() {
		return $this->getParent()->getRelativePath() . $this->getPath();
	}

	public function getIO() {
		return $this->getParent()->getIO();
	}
}
