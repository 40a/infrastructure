<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 01.05.11
 * Time: 23:44
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_Configuration_Constraint {
	const Name_TYPO3 = 'typo3';
	const Name_PHP = 'php';

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var TYPO3_Version
	 */
	protected $lowestVersion;

	/**
	 * @var TYPO3_Version
	 */
	protected $highestVersion;

	public static function create($name, $versionRange) {
		$versions = explode('-', $versionRange, 2);

		$lowestVersion = (isset($versions[0]) && empty($versions[0]) === FALSE ? $versions[0] : '0.0.0');
		$highestVersion = (isset($versions[1]) && empty($versions[1]) === FALSE ? $versions[1] : '0.0.0');

		return new TYPO3_Extension_Configuration_Constraint(
			$name,
			TYPO3_Version::create($lowestVersion),
			TYPO3_Version::create($highestVersion)
		);
	}

	public function __construct($name, TYPO3_Version $lowestVersion, TYPO3_Version $highestVersion) {
		$this->setName($name);
		$this->setLowestVersion($lowestVersion);
		$this->setHighestVersion($highestVersion);
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setLowestVersion(TYPO3_Version $lowestVersion) {
		$this->lowestVersion = $lowestVersion;
		$this->validate();
	}

	public function getLowestVersion() {
		return $this->lowestVersion;
	}

	public function setHighestVersion(TYPO3_Version $highestVersion) {
		$this->highestVersion = $highestVersion;
		$this->validate();
	}

	public function getHighestVersion() {
		return $this->highestVersion;
	}

	public function getVersionRange() {
		$versionRange = '';

		if ($this->lowestVersion->__toInteger() > 0 || $this->highestVersion->__toInteger() > 0) {
			$versionRange = $this->lowestVersion->get() . '-' . $this->highestVersion->get();
		}

		return $versionRange;
	}

	protected function validate() {
		if (isset($this->highestVersion) && $this->highestVersion->__toInteger() > 0) {
			if ($this->lowestVersion->__toInteger() > $this->highestVersion->__toInteger()) {
				throw new LogicException('Version range is invalid.');
			}
		}
	}
}
