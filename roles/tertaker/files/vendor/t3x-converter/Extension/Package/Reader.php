<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 27.04.11
 * Time: 23:18
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_Package_Reader {
	const HEADER_GZipCompressed = 'gzcompress';

	/**
	 * @var string
	 */
	protected $file;

	/**
	 * @var array
	 */
	protected $data;

	public function __construct($file) {
		$this->setFile($file);
	}

	public function get($key) {
		$this->extractData();

		if (isset($this->data[$key]) === FALSE) {
			throw new RuntimeException('Invalid data key "' . $key . '".');
		}

		return $this->data[$key];
	}

	public function setFile($file) {
		$this->file = $file;
	}

	public function getFile() {
		return $this->file;
	}

	/**
	 * @throws RuntimeException
	 * @return array
	 */
	protected function extractData() {
		if (isset($this->data) === FALSE) {
			$parts = explode(':', file_get_contents($this->file), 3);

			if (count($parts) !== 3) {
				throw new RuntimeException('Invalid extension package format.');
			}

			if ($parts[1] === self::HEADER_GZipCompressed) {
				$parts[2] = gzuncompress($parts[2]);
			}

			$result = unserialize($parts[2]);

			if ($parts[0] !== md5($parts[2]) || is_array($result) === FALSE) {
				throw new RuntimeException('Invalid extension package contents.');
			}

			$this->data = $result;
		}
	}
}
