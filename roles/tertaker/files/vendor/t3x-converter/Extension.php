<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 27.04.11
 * Time: 23:09
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension {
	/**
	 * @var TYPO3_Extension_Configuration
	 */
	protected $configuration;

	/**
	 * @var TYPO3_Extension_IO
	 */
	protected $IO;

	/**
	 * @param  $directory
	 * @return TYPO3_Extension
	 */
	public static function read($directory) {
		$IO = new TYPO3_Extension_IO(TYPO3_Extension_IO::CONTEXT_Extension);
		$IO->setFileSystemOrigin($directory);
		$IO->readConfigurationFile();
		$IO->refresh();

		$extension = new TYPO3_Extension(
			TYPO3_Extension_Configuration::read(
				$IO->getConfigurationFile(),
				self::extractKey($directory)
			)
		);

		$extension->setIO($IO);

		return $extension;
	}

	public static function extractKey($directory) {
		$directory = rtrim($directory, DIRECTORY_SEPARATOR);
		$parts = explode(DIRECTORY_SEPARATOR, $directory);

		if (count($parts) === 0) {
			throw new RuntimeException('Extension key could not be extracted from directory.');
		}

		return $parts[count($parts) - 1];
	}

	public function __construct(TYPO3_Extension_Configuration $configuration) {
		$this->setConfiguration($configuration);
	}

	public function setIO(TYPO3_Extension_IO $IO) {
		$this->IO = $IO;
	}

	public function getIO() {
		return $this->IO;
	}

	public function setConfiguration(TYPO3_Extension_Configuration $configuration) {
		$this->configuration = $configuration;
	}

	public function getConfiguration() {
		return $this->configuration;
	}
}
