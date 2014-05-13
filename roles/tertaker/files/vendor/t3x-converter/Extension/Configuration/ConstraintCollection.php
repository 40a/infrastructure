<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 01.05.11
 * Time: 23:44
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_Configuration_ConstraintCollection {
	const KEY_Dependencies = 'depends';
	const KEY_Conflicts = 'conflicts';
	const KEY_Suggests = 'suggests';

	protected $dependencies = array();

	protected $conflicts = array();

	protected $suggests = array();

	public static function create(array $array = NULL) {
		$constraintCollection = new TYPO3_Extension_Configuration_ConstraintCollection();

		if (isset($array[self::KEY_Dependencies])) {
			$constraintCollection->setDependencies(
				self::createFromConstraintArray($array[self::KEY_Dependencies])
			);
		}
		if (isset($array[self::KEY_Conflicts])) {
			$constraintCollection->setConflicts(
				self::createFromConstraintArray($array[self::KEY_Conflicts])
			);
		}
		if (isset($array[self::KEY_Suggests])) {
			$constraintCollection->setSuggests(
				self::createFromConstraintArray($array[self::KEY_Suggests])
			);
		}

		return $constraintCollection;
	}

	protected static function createFromConstraintArray(array $array) {
		$constraints = array();

		foreach ($array as $name => $versionRange) {
			$constraints[] = TYPO3_Extension_Configuration_Constraint::create($name, $versionRange);
		}

		return $constraints;
	}

	public function __toArray() {
		return array(
			self::KEY_Dependencies => $this->__toConstraintArray($this->dependencies),
			self::KEY_Conflicts => $this->__toConstraintArray($this->conflicts),
			self::KEY_Suggests => $this->__toConstraintArray($this->suggests),
		);
	}

	protected function __toConstraintArray(array $constraints) {
		$array = array();

		/** @var $contraint TYPO3_Extension_Configuration_Constraint */
		foreach ($constraints as $contraint) {
			$array[$contraint->getName()] = $contraint->getVersionRange();
		}

		return $array;
	}

	public function setDependencies(array $dependencies) {
		$this->validateConstraints($dependencies);
		$this->dependencies = $dependencies;
	}

	public function getDependencies() {
		return $this->dependencies;
	}

	public function setConflicts(array $conflicts) {
		$this->validateConstraints($conflicts);
		$this->conflicts = $conflicts;
	}

	public function getConflicts() {
		return $this->getConflicts();
	}

	public function setSuggests(array $suggests) {
		$this->validateConstraints($suggests);
		$this->suggests = $suggests;
	}

	public function getSuggests() {
		return $this->suggests;
	}

	protected function validateConstraints(array $constraints) {
		foreach ($constraints as $constraint) {
			if ($constraint instanceof TYPO3_Extension_Configuration_Constraint === FALSE) {
				throw new RuntimeException('Type TYPO3_Extension_Configuration_Constraint expected.');
			}
		}
	}
}
