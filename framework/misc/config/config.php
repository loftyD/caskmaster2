<?php
/**
 *  config.php
 *	Sets a number of properties for this project. Settings for PDO can be altered here.
 */
use flight\Engine;
$app = new Engine();

	$app->set("db.vendor", "mysql");
	$app->set("db.host","192.168.1.241");
	$app->set("db.name","caskmaster");
	$app->set("db.user","root");
	$app->set("db.password","admin");
	$app->set("flight.log_errors", true);
	$app->set("flight.views.path",$_SERVER['DOCUMENT_ROOT'] . "/framework/views");
	$app->set("caskmaster.environment","production");
	$app->set("caskmaster.version","2.20");
	$app->set("redis.host","127.0.0.1");
	$app->set("redis.port", 6379);


/**
 *	REDIS - Change Config for REDIS above
 */
	$redisSettings = array(
	    "host" => $app->get("redis.host"),
	    "port" => $app->get("redis.port"),
	    "database" => ($app->get("caskmaster.environment") == "development" ? 15 : 0),
	);

	$app->register("redis","Predis\Client", array($redisSettings) );
