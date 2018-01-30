<?php
/**
 *	RoutingManager.php
 *	Put all your routes here
 */
include "autoload.php";
include "misc/config/config.php";

// store our Flight instance into a superglobal. This can then be used in other classes
$app->set("caskmaster.version", $app->options()->get("caskmaster.version"));
$_SERVER['app'] = $app;


/*
 * Put your Flight Routes below here
 * Please check the FlightPHP documentation on how to set up routes. For further information visit http://flightphp.com/
 */

$app->route('/admin(/)(@page)', function($page) use($app) {
    $controller = new controllers\AdminController();
    $controller->run($page);
});

$app->route('/(@page)', function($page) use ($app) {
    $controller = new controllers\SiteController();
    $controller->run($page);
});


$app->start();