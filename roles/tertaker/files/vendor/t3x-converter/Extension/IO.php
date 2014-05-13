<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 28.04.11
 * Time: 00:14
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_IO {
	const IGNORE_FilePattern = '^\.+(git.*|svn.*)?$';

	const FILE_Configuration = 'ext_emconf.php';
	const KEY_Name = 'name';
	const KEY_Size = 'size';
	const KEY_ModificationTime = 'mtime';
	const KEY_Executable = 'is_executable';
	const KEY_Content = 'content';
	const KEY_Hash = 'content_md5';

	const CONTEXT_Package = 'package';
	const CONTEXT_Extension = 'extension';

	/**
	 * @var TYPO3_Extension_IO_RootDirectory
	 */
	protected $rootDirectory;

	/**
	 * @var string
	 */
	protected $context;

	/**
	 * @var string
	 */
	protected $fileSystemOrigin;

	public function __construct($context) {
		$this->setContext($context);
		$this->rootDirectory = new TYPO3_Extension_IO_RootDirectory($this);
	}

	public function __toArray() {

	}

	public function __toTree() {
		$this->getRootDirectory()->__toTree();
	}

	public function getAllFiles() {
		return $this->getRootDirectory()->getAllFiles();
	}

	public function setFileSystemOrigin($fileSystemOrigin) {
		$this->fileSystemOrigin = rtrim($fileSystemOrigin, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	public function getFileSystemOrigin() {
		return $this->fileSystemOrigin;
	}

	public function getRootDirectory() {
		return $this->rootDirectory;
	}

	public function readConfigurationFile() {
		if ($this->getContext() !== self::CONTEXT_Extension) {
			throw new RuntimeException('Refreshing IO is only possible in extension context.');
		}

		$this->setConfigurationFile(
			TYPO3_Extension_IO_File::read($this->getFileSystemOrigin() . self::FILE_Configuration)
		);
	}

	/**
	 * @return TYPO3_Extension_IO_File
	 */
	public function getConfigurationFile() {
		return $this->getRootDirectory()->getFile(self::FILE_Configuration);
	}

	public function setConfigurationFile(TYPO3_Extension_IO_File $file) {
		$file->setName(self::FILE_Configuration);
		$this->getRootDirectory()->setFile($file);
	}

	public function setContext($context) {
		if ($context !== self::CONTEXT_Extension && $context !== self::CONTEXT_Package) {
			throw new LogicException('Invalid context');
		}

		$this->context = $context;
	}

	public function getContext() {
		return $this->context;
	}

	public function refresh() {
		if ($this->getContext() !== self::CONTEXT_Extension) {
			throw new RuntimeException('Refreshing IO is only possible in extension context.');
		}

		$this->refreshDirectory($this->getFileSystemOrigin());
	}

	protected function refreshDirectory($fullPath, $relativePath = '') {
		$resources = scandir($fullPath . $relativePath);
		$directory = $this->getRootDirectory()->walk($relativePath, TRUE);

		foreach ($resources as $resource) {
			if (preg_match('/' . self::IGNORE_FilePattern . '/i', $resource)) {
				continue;
			}

			$resourcePath = $fullPath . $relativePath . $resource;

			if (is_dir($resourcePath)) {
				$this->refreshDirectory(
					$fullPath,
					$relativePath . rtrim($resource, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
				);
			} elseif (is_file($resourcePath)) {
				if ($directory->hasFile($resource) === FALSE) {
					$directory->addFile(
						TYPO3_Extension_IO_StructureFile::create($directory, $resourcePath)
					);
				}
			}
		}
	}
}
