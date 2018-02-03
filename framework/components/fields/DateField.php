<?php
/**
 * DateField.php
 * This field renders a date picker element.
 */
namespace components\fields;
class DateField extends BaseField {

	protected $javaScript = "<script>
	$( function() {
		$('.datepicker').datepicker({
		    dateFormat: 'yy-mm-dd'
		});
	});
	</script>";
	
	public function render() {
		$html = parent::render();
		$html .= "<input type=\"text\" name=\"$this->name\" id=\"field_$this->name\" class=\"form-control datepicker\"  ". $this->setApplicableValue() . "/>";
		return $html;
	}

	public function validate() {
		if(parent::validate() == false) {
			return false;
		} elseif(\DateTime::createFromFormat('Y-m-d', $this->value) === false) {
			return false;
		}

		return true;
	}
}