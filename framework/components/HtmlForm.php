<?php
/**
 * HtmlForm.php
 * Handles the creation, validation and displaying of our HTML Form.
 */
namespace components;
use flight\Engine;
class HtmlForm {

	private $name   = "";
	private $fields = array();
	private $form   = array();
	public $preserveValues = false;
	protected $router;

	/**
	 * @param string $name The Name of this form. This will be displayed in an <h1> element.
	 * @param string $method How the form will be submitted via GET or POST request.
	 * @param string $action Corresponds to the action attribute of the html form. (The destination of this form)
	 */
	public function __construct($name="",$method="POST",$action) {
		if($name == "" || empty($name)) {
			throw new \Exception('HtmlForm::$name must be defined.');
		}

		if($method == "" || empty($method)) {
			throw new \Exception('HtmlForm::$method must be defined.');
		}

		if($action == "" || empty($action)) {
			throw new \Exception('HtmlForm::$action must be defined.');
		}

		$this->name = $name;
		$this->form['begin'] = "<form action=\"$action\" method=\"$method\">";
		$this->form['end']   = "</form>";

		$this->router = new Engine();
	}

	/**
	 * Returns the name of this form.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the HTML Opening and Closing Tags that have been created via __construct()
	 * @param  enum(begin,end) Whether to return the opening or closing HTML of the form.
	 * @return string
	 */
	public function getFormString($location="begin") {
		if(!in_array($location, array("begin","end"))) {
			throw new \Exception("HtmlForm::getFormString() argument 1 must be specified as 'begin' or 'end'");
		}
		return $this->form[$location];
	}

	/**
	 * Builds an array of fields that will be displayed on the form.
	 * @param  string $fieldName The name of the field
	 * @param  string $fieldClass The name of the class that will render the field
	 */
	public function loadField($fieldName,$fieldClass) {
		$this->fields[$fieldName] = $fieldClass;
	}

	/**
	 * When this method is called, the entire contents of the form are rendered on screen. 
	 * The method returns the html rather than displaying on screen.
	 * @return string 
	 */
	public function renderForm() {
		$html = "";

		$html.="<h1>" . $this->getName() . "</h1>";

		$html .= $this->getFormString("begin");
		foreach($this->fields as $fieldName => $fieldClass) {
			$class = "\\components\\fields\\$fieldClass";
			$field = new $class($fieldName, $this->preserveValues);
			$html.= $field->render();
		}

		$html .= "<br /><input type=\"submit\" class=\"btn btn-primary\" name='btn' value=\"Submit\">";
		$html .= $this->getFormString("end");
		return $html;
	}

	/**
	 * Loads each form field and ensures that the fields have passed the necessary validation
	 * @return boolean true if all fields passed validation
	 */
	public function validateFields() {

		foreach($this->fields as $fieldName => $fieldClass) {
			$class = "\\components\\fields\\$fieldClass";
			$field = new $class($fieldName, $this->preserveValues);

			if($field->validate() === false) {
				return false;
			}
		}

		return true;
	}
}