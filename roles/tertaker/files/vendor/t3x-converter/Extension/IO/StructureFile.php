<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 28.04.11
 * Time: 00:15
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_IO_StructureFile extends TYPO3_Extension_IO_File {
	public static function create(TYPO3_Extension_IO_Directory $directory, $file) {
		if (!file_exists($file)) {
			throw new RuntimeException('File "' . $file . '" not found');
		}

		return new TYPO3_Extension_IO_StructureFile($directory, basename($file));
	}

	public function __construct(TYPO3_Extension_IO_Directory $directory, $name) {
		$this->setDirectory($directory);
		$this->setName($name);
	}

	public function getSize() {
		return filesize($this->getFullPath());
	}

	public function getModificationTime() {
		return filemtime($this->getFullPath());
	}

	public function getExecutable() {
		return is_executable($this->getFullPath());
	}

	public function getContent() {
		return file_get_contents($this->getFullPath());
	}

	public function getHash() {
		return md5_file($this->getFullPath());
	}
}
