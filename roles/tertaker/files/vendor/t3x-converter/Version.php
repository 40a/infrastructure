<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 01.05.11
 * Time: 23:44
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Version {
	const PART_Major = 0;
	const PART_Minor = 1;
	const PART_Patch = 2;

	protected $version;

	/**
	 * @var array
	 */
	protected $allowedParts = array(self::PART_Major, self::PART_Minor, self::PART_Patch);

	public static function create($version) {
		return new TYPO3_Version($version);
	}

	public function __construct($version) {
		$this->set($version);
	}

	public function __toInteger() {
		$string = '';

		$parts = explode('.', $this->get());
		foreach ($parts as $index => $part) {
			$part = intval($part);
			if ($index === 0) {
				$string .= $part;
			} else {
				$string .= str_pad($part, 3, '0', STR_PAD_LEFT);
			}
		}

		return intval($string);
	}

	public function __toString() {
		return $this->version;
	}

	public function get() {
		return $this->version;
	}

	public function set($version) {
		if (count(explode('.', $version)) !== 3) {
			throw new LogicException('Version "' . $version . '" is not valid.');
		}

		$this->version = $version;
	}

	public function increment($part) {
		if (in_array($part, $this->allowedParts) === FALSE) {
			throw new LogicException('The defined part is not defined');
		}

		$parts = explode('.', $this->get());
		$parts[$part] = intval($parts[$part]) + 1;

		$this->set(implode('.', $parts));
	}
}
