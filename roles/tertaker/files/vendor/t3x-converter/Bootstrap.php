<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 27.04.11
 * Time: 23:49
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Bootstrap {
	public static function execute() {
		spl_autoload_register('TYPO3_Bootstrap::autoload');
	}

	public static function autoload($className) {
		$classParts = explode('_', $className);

		if (count($classParts) > 1 && $classParts[0] === 'TYPO3') {
			$directoryName = implode(DIRECTORY_SEPARATOR, array_slice($classParts, 1, -1));
			$fileName = implode(array_slice($classParts, -1)) . '.php';

			$filePath = ($directoryName ? $directoryName . DIRECTORY_SEPARATOR : '') . $fileName;
			require_once $filePath;
		}
	}
}
