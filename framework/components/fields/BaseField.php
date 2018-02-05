<?php
/**
 * BaseField.php
 * All form fields should extend this class. 
 * BaseField handles the rendering and validation of each form element on a form.
 */
namespace components\fields;
use flight\Engine;
abstract class BaseField {

	protected $label = "";
	protected $name = "";
	protected $javaScript = "";
	protected $value = "";
	protected $router;
	protected $preserveValues;

	/**
	 * Constructor for BaseField.
	 * @param string $name The name of this field
	 * @param boolean $preserveValues If the form has been already submitted, we want to display what the user had entered.
	 */
	final public function __construct($name,$properties=array()) {
		if($name == "" || empty($name)) {
			throw new \Exception('BaseField::$name must be defined');
		}

		$this->name = $name;
		if(!empty($properties)) {
			foreach($properties as $property => $propertyValue) {
				$this->{$property} = $propertyValue;
				$rp = new \ReflectionProperty($this,$property);
				if($rp->isPrivate() || $rp->isProtected()) {
					continue;
				}
				
			}
		}



		if(empty($this->preserveValues)) {
			if(!isset($_POST)) {
				$this->preserveValues = false;
			} else  {
				$this->preserveValues = true;
			}
		}
		if(empty($this->{'fieldLabel'})) {
			$this->{'fieldLabel'} = $this->convertNameToLabel($name);
		}

		$this->router = new Engine();
		if($this->preserveValues) {
			$this->preserveValues = true;
			if(isset($this->{'dynamicFormLoadedModel'})) {
				if($this->router->request()->method == 'POST') {
					$this->value = htmlspecialchars($this->router->request()->data->{$name},ENT_QUOTES,'UTF-8');
				} else {
					$this->value = htmlspecialchars($this->dynamicFormLoadedModel->{$name},ENT_QUOTES,'UTF-8');
				}
			} else {
				if($this->router->request()->method == 'GET') {
					$this->value = htmlspecialchars($this->router->request()->query->{$name},ENT_QUOTES,'UTF-8');
				} else {
					$this->value = htmlspecialchars($this->router->request()->data->{$name},ENT_QUOTES,'UTF-8');
				}
			}
		}

	}

	/**
	 * Should any child elements not have label defined, we will construct one.
	 * @param  string
	 * @return string
	 */
	private function convertNameToLabel($str) {
		$label = explode("_",$str);

		if(count($label) > 1) {
			$label = array_map('ucfirst',$label);
			$label = implode(" ",$label);
		} else {
			$label = ucfirst($str);
		}

		return $label;
	}

	/**
	 * Returns the HTML for this label.
	 * @return string
	 */
	protected function generateLabel() {
		$html = "<label for=\"field_$this->name\">".$this->{'fieldLabel'}."</label>";
		return $html;
	}

	/**
	 * Render the form field, along with any applicable JavaScript that we wish to apply on this field.
	 * @return string
	 */
	public function render() {
		return $this->applyJavaScript() . $this->generateLabel();
	}

	/**
	 * Returns the JavaScript that is associated with this form field.
	 * @return string
	 */
	public function applyJavaScript() {
		return $this->javaScript;
	}

	/**
	 * Returns the HTML needed to display the original value of this form field. 
	 * This method will need to be overwritten for more complex form elements, 
	 * such as Radio Buttons and Text Areas.
	 */
	public function setApplicableValue() {
		$html = "";
		if($this->preserveValues) {
			$html = "value = \"$this->value\"";
		}
		return $html;
	}

	/**
	 * Validates the form element.
	 * At a basic level, this will validate whether a field is required or optional.
	 * @return boolean
	 */
	public function validate() {
		if(empty($this->value) && (isset($this->required) && $this->required == true) ) {
			return false;
		}

		return true;
	}

}
