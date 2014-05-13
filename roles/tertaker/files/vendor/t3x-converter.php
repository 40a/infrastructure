#!/usr/bin/env php
<?php

if ($argv[1]) {
	require_once('t3x-converter/Bootstrap.php');
	TYPO3_Bootstrap::execute();

	$srcPath    = $argv[1];
	$targetPath = $argv[2];

	/**
	 * Import T3X File
	 */
	$package = TYPO3_Extension_Package::read($srcPath);
	$targetPath = $targetPath . $package->getName();
	$values = $package->getConfiguration();
#	var_dump($values->state);
	$package->writeTo($targetPath);

	# Fill the git repository
	if (is_dir($targetPath) === FALSE) {
		$package->writeTo($targetPath);
	#	$package->commit();
	#	$package->tag();
	}
}