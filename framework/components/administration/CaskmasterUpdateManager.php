<?php
/**
 * CaskmasterUpdateManager.php
 * Administers the Update Process
 */
namespace components\administration;
set_time_limit(-1);
use components\VarDumper;
class CaskmasterUpdateManager {

	protected $xml;
	protected $update;
	private   $target_version;

	public function __construct() {
		$this->xml = $_SERVER['DOCUMENT_ROOT'] . '/framework/misc/update/update.xml';

		if(!file_exists($this->xml)) {
			throw new \Exception("No update.xml found, this must reside in framework/misc/update/update.xml");
		}

		if(filesize($this->xml) == 0) {
			throw new \Exception("update.xml found, but empty file detected");
		}

		$this->update = simplexml_load_file($this->xml);

	}

	public function displayUpgradeSteps() {
		if(empty($this->update['target_version'])) {
			throw new \Exception("target_version not defined.");
		}

		$this->target_version = $this->update['target_version'];

		$html = "";
		$currentVersion = $_SERVER['app']->get("caskmaster.version");
		$html = "<h2>Update Caskmaster From Version " . $currentVersion . " to " .$this->target_version . "</h2>";
		$html .= $this->loopThroughSteps(true);
		return $html;
	}


	private function loopThroughSteps($runUpgrade=false) {
		$html = "<div class='well'>";
		foreach($this->update as $step) {
			$id = $step['id'];
			$lastStep = ((!empty($step['last'])) || $step['last'] == '1');
			$cmd = $step->command;
			$desc = $step->description;
			$html.= "<p id=\"step_$id\">";
			$html .= "<strong class=\"cmd\">Step $id : $cmd</strong><br />";
			$html .= "<span class=\"desc\">$desc</span><br />";
			$html .= "</p>";

			if($runUpgrade) {
				$from = $to = "";
				$splitCmd = explode(" ", $cmd);
				foreach($splitCmd as $each) {
					
					if(empty($from)) {
						$from = $this->returnKeyWordsFromCommand($each,"config");

						continue;
					}
					if(empty($to)) {
						$to = $this->returnKeyWordsFromCommand($each,"config");
						continue;
					}
				}

				foreach($splitCmd as $each) {

					if(empty($from)) {
						$from = $this->returnKeyWordsFromCommand($each,"file");
						continue;
					}
					if(empty($to)) {
						$to = $this->returnKeyWordsFromCommand($each,"file");
						continue;
					}
				}

				if(empty($to)) {
					$mode = "file";
				} else {
					$mode = "config";
				}
			}
		}
		$html .= "</div>";
		return $html;
	}

	private function returnKeyWordsFromCommand($str, $type) {
		if($type == "config")
			$result = $this->getStringBetween($str,"#", "#");
		if($type == "file")
			$result = $this->getStringBetween($str,"@", "@");

		return $result;

	}

	private function getStringBetween($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

}