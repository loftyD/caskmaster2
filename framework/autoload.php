<?php
/**
 *  autoload.php
 *	Autoloader for this project. Do not modify
 */
require $_SERVER['DOCUMENT_ROOT'] . "/lib/flight/flight/autoload.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";


spl_autoload_register(function($class) {
	// we are not interested in autoloading any FlightPHP dependencies
	if(!preg_match("*flight*",$class) || !preg_match("*Predis*",$class)) {
		$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		include($filename);
	}
});

