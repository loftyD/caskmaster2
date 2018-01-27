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

		$currentVersion = (float) $_SERVER['app']->get("caskmaster.version");
		$targetVersion =  (float) $this->update['target_version'];

		if($currentVersion == $targetVersion) {
			throw new \Exception("Current is same as target version.");
		}

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
					$this->checkoutBranch($from);
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

	private function checkoutBranch($branch) {
		$repoLocation = $_SERVER['DOCUMENT_ROOT'] . '/framework/misc/update/repo';


		$this->deleteResource($repoLocation);
		
		$repo = \Cz\Git\GitRepository::cloneRepository('https://github.com/loftyD/caskmaster2.git', $repoLocation);
		if($repo->hasChanges()) {
			if(!in_array($branch,$repo->getBranches())) {
				throw new \Exception("Chosen branch is not recognised");
			}

			$repo->checkout($branch);
		}

		$loadGitIgnoreFiles = file($repoLocation.'/.gitignore');
		foreach($loadGitIgnoreFiles as $eachFile) {
			$eachFile = ltrim($eachFile,"/");
			if(file_exists($repoLocation.'/'.$eachFile)) {
				unlink($repoLocation.'/'.$eachFile);
			}
			if(is_dir($repoLocation.'/'.$eachFile)) {
				$this->deleteResource($repoLocation.'/'.$eachFile);
			}
		}

		$this->copy($repoLocation,$_SERVER['DOCUMENT_ROOT']);

		$this->deleteResource($repoLocation);

		return true;
		
	}

	private function deleteResource($dir) {

	    if (empty($dir)) { 
    		return false;
		}

		if (is_dir($dir)) {
    		$objects = scandir($dir);
    		foreach ($objects as $object) {
      			if ($object != "." && $object != "..") {
        			if (filetype($dir."/".$object) == "dir") 
           				$this->deleteResource($dir."/".$object); 
        			else 
        				unlink($dir."/".$object);
      			}
    		}
    		reset($objects);
    		rmdir($dir);
  		}
	}

	private function copy($source, $target) {
        if (!is_dir($source)) {//it is a file, do a normal copy
            copy($source, $target);
            return;
        }

        //it is a folder, copy its files & sub-folders
        @mkdir($target);
        $d = dir($source);
        $navFolders = array('.', '..');
        while (false !== ($fileEntry=$d->read() )) {//copy one by one
            //skip if it is navigation folder . or ..
            if (in_array($fileEntry, $navFolders) ) {
                continue;
            }

            //do copy
            $s = "$source/$fileEntry";
            $t = "$target/$fileEntry";
            $this->copy($s, $t);
        }
        $d->close();
    }

}