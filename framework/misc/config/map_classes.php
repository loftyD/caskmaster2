<?php
	
	$sessionProvider = new EasyCSRF\NativeSessionProvider();

	$redisSettings = array(
	    "host" => $app->get("redis.host"),
	    "port" => $app->get("redis.port"),
	    "database" => ($app->get("caskmaster.environment") == "development" ? 15 : 0),
	);

	
	$app->register("redis","Predis\Client", array($redisSettings) );
	$app->register("options","\components\Options",array($app) );
	$app->register("csrf","\components\EasyCSRFExtended",array($sessionProvider));