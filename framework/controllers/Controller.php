<?php
/**
 * Controller.php
 * All controllers should extend off this class.
 */
namespace controllers;
use flight\Engine;

abstract class Controller {

	public $router;
	protected $title;
	protected $viewsPath;
	protected $json=false;

	/**
	 * Controller Constructor
	 */
	final public function __construct() {
		ob_start();
		include($_SERVER['DOCUMENT_ROOT'] . "/framework/misc/config/config.php");
		$this->router = $app;
		$this->viewsPath = $_SERVER['DOCUMENT_ROOT'] . "/framework/views";
		$this->router->set("flight.views.path",$this->viewsPath);
		$this->init();
	}

	/**
	 * Displays the header.
	 * If you need to point to a different view file then you should override this method.
	 */
	protected function init() {
		if($this->json == false) {
			$controller = new \ReflectionClass($this);
			$this->router->render("main/head",array(
				"pageTitle" => $this->title, 
				"controller" => $controller->getShortName(),
				)
			);

		} else {
			ob_end_clean();
		}
	}

	/**
	 * Displays the footer.
	 * If you need to point to a different view file then you should override this method.
	 */
	protected function deInit() {
		if($this->json == false) {
			$this->router->render("main/foot");
		}
	}

	final public function __destruct() {
		if($this->json) {
			header("Content-Type: application/json");
			$contents = ob_get_contents();
		}
    	$this->deInit();
    }

    /**
     * All extended controllers need a actionHome()
     */
	protected function actionHome() {

	}
}