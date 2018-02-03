<?php
/**
 * SiteController.php
 * Handles the necessary actions for this project.
 */
namespace controllers;
use \components\VarDumper;
use \components\Barrel;
class AdminController extends Controller {

	protected $title = "Caskmaster 2.0 :- Admin";


	protected function init() {
		if(!isset($_SESSION['username'])) {
			$this->router->redirect("/login");
		}
		parent::init();
	}

	/**
	 * Handles the applicable actions for SiteController. Should someone enter
	 * an invalid action, Flight will return its default notFound status page.
	 * @param  string $page The page, requested by the web user
	 * @return [type]
	 */
	public function run($page) {
		if($page == "" || empty($page)) {
			$this->actionHome();
		} elseif($page == "logout") {
			$this->actionLogout();
		} elseif($page == "update") {
			$this->actionUpdate();
		} elseif($page == "form") {
			$this->actionTest();
		} else {
			$this->router->notFound();
		}
	}

	protected function actionHome() {
		$data = Barrel::returnLatestSessionData();
		$dataTable = new \components\DataTable();
		$dataTable->setPrimaryKeyField("company_id");
		$dataTable->setTableHeader("My Assigned Companies");
		$dataTable->setTableContents($data->assigned_companies);
		$dataTable->setCellHidden(array(
			"user_id","flowchart_step_id","highlight","status","added","updated",
			"product_id_list","image","bio","samples_provided","alert_date",
			)
		);
		$dataTable->isDataTable();
		$dataTable->setRowClickLink("admin/company/");

		$version = \components\Barrel::getLatestVersion();
		$this->router->render("admin/home",array("latest" => $version, "assigned_companies" => $dataTable->generate()));
	}

	protected function actionLogout() {
		$authentication = new \components\Authentication();
		$authentication->logout();
		$this->router->redirect("/");
	}

	protected function actionUpdate() {
		$updateManager = new \components\administration\CaskmasterUpdateManager();
		$this->router->render("admin/update", array("update" => $updateManager->displayUpgradeSteps()));
	}

	protected function actionTest() {
		
		$dynamicForm = (new \models\DynamicForm())->getMainForm('User');
		$htmlForm = new \components\HtmlForm("UserDB","POST","/admin/form");
		echo "<div class=\"container\">";
			echo $htmlForm->renderDynamicForm($dynamicForm);
		echo "</div>";

	}
}