<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 28.04.11
 * Time: 00:15
 * To change this template use File | Settings | File Templates.
 */
 
abstract class TYPO3_Extension_IO_Directory {
	/**
	 * @var array
	 */
	protected $files = array();

	/**
	 * @var array
	 */
	protected $directories = array();

	public function __toTree($depth = 0) {
		/** @var $file TYPO3_Extension_IO_File */
		foreach ($this->files as $file) {
			$file->__toTree($depth + 1);
		}

		/** @var $directory TYPO3_Extension_IO_SubDirectory */
		foreach ($this->directories as $directory) {
			$directory->__toTree($depth + 1);
		}
	}

	public function getAllFiles() {
		$resources = array();

		/** @var $file TYPO3_Extension_IO_File */
		foreach ($this->files as $file) {
			$resources[] = $file;
		}

		/** @var $directory TYPO3_Extension_IO_SubDirectory */
		foreach ($this->directories as $directory) {
			$resources = array_merge($resources, $directory->getAllFiles());
		}

		return $resources;
	}

	public function addFile(TYPO3_Extension_IO_File $file) {
		if ($this->hasFile($file->getName())) {
			throw new RuntimeException('File "' . $file->getName() . '" already exists');
		}

		$this->files[$file->getName()] = $file;
		$file->setDirectory($this);
	}

	public function setFile(TYPO3_Extension_IO_File $file) {
		$this->files[$file->getName()] = $file;
		$file->setDirectory($this);
	}

	public function hasFile($fileName) {
		return isset($this->files[$fileName]);
	}

	public function getFile($fileName) {
		if ($this->hasFile($fileName)) {
			return $this->files[$fileName];
		}
	}

	public function addDirectory(TYPO3_Extension_IO_SubDirectory $directory) {
		if (is_null($directory->getParent()) === FALSE) {
			throw new RuntimeException('Directory is already attached to another parent directory.');
		}

		$directory->setParentDirectory($this);
		$this->directories[$directory->getName()] = $directory;
	}

	public function hasDirectory($name) {
		return (isset($this->directories[$name]));
	}

	/**
	 * @param string $name
	 * @return TYPO3_Extension_IO_Directory
	 */
	public function getDirectory($name) {
		$directory = NULL;

		if ($this->hasDirectory($name)) {
			$directory = $this->directories[$name];
		}

		return $directory;
	}

	/**
	 * @throws RuntimeException
	 * @param string $path
	 * @param boolean $create
	 * @return TYPO3_Extension_IO_Directory
	 */
	public function walk($path, $create = FALSE) {
		$directory = $this;

		$path = rtrim($path, DIRECTORY_SEPARATOR);
		if (empty($path) === FALSE && $path !== '.') {
			$pathSegments = explode(DIRECTORY_SEPARATOR, $path);

			foreach ($pathSegments as $pathSegment) {
				if ($directory->hasDirectory($pathSegment)) {
					$directory = $directory->getDirectory($pathSegment);
				} elseif ($create) {
					$directory->addDirectory(new TYPO3_Extension_IO_SubDirectory($pathSegment));
					$directory = $directory->getDirectory($pathSegment);
				} else {
					throw new RuntimeException('Directory "' . $pathSegment . '" not found.');
				}
			}
		}

		return $directory;
	}

	public function writeTo($path) {
		$path = $this->sanitize($path) . $this->getPath();

		if (is_dir($path) === FALSE) {
			// @todo Defined chmod
			// TODO ct 2011-07-08 create dirs recursive
			mkdir($path);
		}

		/** @var $file TYPO3_Extension_IO_File */
		foreach ($this->files as $file) {
			$file->writeTo($path);
		}

		/** @var $directory TYPO3_Extension_IO_Directory */
		foreach ($this->directories as $directory) {
			$directory->writeTo($path);
		}
	}

	protected function sanitize($directory) {
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$directory = preg_replace('#\./#', '', $directory);
		return $directory;
	}

	abstract public function getPath();

	abstract public function getFullPath();

	abstract public function getRelativePath();

	/**
	 * @abstract
	 * @return TYPO3_Extension_IO
	 */
	abstract public function getIO();
}
