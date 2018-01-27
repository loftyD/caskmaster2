<?php
/**
 * SiteController.php
 * Handles the necessary actions for this project.
 */
namespace controllers;

class SiteController extends Controller {

	protected $title = "Caskmaster 2.0";

	/**
	 * Handles the applicable actions for SiteController. Should someone enter
	 * an invalid action, Flight will return its default notFound status page.
	 * @param  string $page The page, requested by the web user
	 * @return [type]
	 */
	public function run($page) {
		if($page == "" || empty($page)) {
			$this->actionHome();
		} elseif($page == "login") {
			$this->actionLogin();
		} elseif($page == "v") {
			$this->json = true;
			$this->actionVersion();
		} else {
			$this->router->notFound();
		}
	}

	/**
	 * Displays the home page for this project.
	 */
	protected function actionHome() {
		$this->router->render("home");
	}

	protected function actionLogin() {
		$error = "";
		$authentication = new \components\Authentication();
		$loginForm = new \components\forms\LoginForm();
		if(!isset($_SESSION['username'])) {
			if(isset($_POST['btn'])) {
				$loginForm->preserveValues = true;
				if($loginForm->validateFields()) {
					if($authentication->login($_POST['username'], $_POST['password'])) {
						$this->router->redirect("/admin");
					} else {
						$error = "The details you have given are invalid.";
					}
				} else {
					$error = "Please ensure you have entered a username and a password.";
				}
			}
			$html = $loginForm->renderForm();
		} else {
			$this->router->redirect("/admin");
		}
		$this->router->render("login", array("form" => $html, "error" => $error));
	}

	public function actionVersion() {
		$this->init();
		if($_SERVER['app']->get("caskmaster.environment") != "production") {
			echo json_encode(array("version" => number_format(mt_rand($_SERVER['app']->get("caskmaster.version")/2*10,25)/10,2) ));
		} else {
			$this->router->notFound();
		}
	}
}