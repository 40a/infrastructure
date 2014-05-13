<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 28.04.11
 * Time: 00:15
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_IO_RootDirectory extends TYPO3_Extension_IO_Directory {
	/**
	 * @var TYPO3_Extension_IO
	 */
	protected $IO;

	public function __construct(TYPO3_Extension_IO $IO) {
		$this->setIO($IO);
	}

	public function __toTree($depth = 0) {
		echo '[RootDirectory: ' . $this->getRelativePath(). ']' . PHP_EOL;
		parent::__toTree();
	}

	public function setIO(TYPO3_Extension_IO $IO) {
		$this->IO = $IO;
	}

	public function getIO() {
		return $this->IO;
	}

	public function getPath() {
		return '';
	}

	public function getFullPath() {
		$fullPath = $this->getIO()->getFileSystemOrigin();

		if (is_null($fullPath)) {
			throw new RuntimeException('Root directory is not attached to file system.');
		}

		return $fullPath;
	}

	public function getRelativePath() {
		return '';
	}
}
