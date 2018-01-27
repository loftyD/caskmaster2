<?php
/**
 * DateExistence.php
 * Our class to calculate the years , hours and days for a web visitor.
 */
namespace components;
use \models\UserExistence;
class DateExistence {

	private $userExistence;

	/**
	 * The constructor for DateExistence
	 * @param UserExistence
	 */
	public function __construct(UserExistence $userExistence) {
		if(empty($userExistence->dob)) {
			throw new \Exception("Date of Birth Property Not Found.");
		}
		$this->userExistence = $userExistence;
	}

	/**
	 * Handles the date difference calculcation.
	 * @return object
	 */
	public function calculate() {
		$now = new \DateTime();
		$dob = new \DateTime($this->userExistence->dob);
		return $dob->diff($now);
	}
}