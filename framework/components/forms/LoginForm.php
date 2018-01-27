<?php
/**
 * LoginForm.php
 * Handles the creation, validation and displaying of our HTML Form.
 */
namespace components\forms;
use flight\Engine;
use components\HtmlForm;
class LoginForm extends HtmlForm {

	public function __construct($name="Login", $method="POST",$action="/login") {
		$this->loadField("username","TextField");
		$this->loadField("password","PasswordField");
		return parent::__construct($name,$method,$action);
	}

}