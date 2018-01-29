<?php
	
	$app->register("redis","Predis\Client", array($redisSettings) );
	$app->register("options","\components\Options");