<?php
/**
 * CaskmasterUpdateManager.php
 * Administers the Update Process
 */
namespace components\administration;
set_time_limit(-1);
use components\VarDumper;
use components\exception\HttpException;
class CaskmasterUpdateManager {

	protected $xml;
	protected $update;
	private   $target_version;

	public function __construct($runChecks=true) {
		if($runChecks) {

			$version = \components\Barrel::getLatestVersion();

			$this->xml = $_SERVER['DOCUMENT_ROOT'] . '/framework/misc/update/update.xml';

			if(!file_exists($this->xml)) {
				throw new HttpException("No update.xml found, this must reside in framework/misc/update/update.xml");
			}

			if(filesize($this->xml) == 0) {
				throw new HttpException("update.xml found, but empty file detected.");
			}

			$this->update = simplexml_load_file($this->xml);

			$currentVersion = (float) $_SERVER['app']->get("caskmaster.version");
			$targetVersion =  (float) $this->update['target_version'];

			if($currentVersion == $targetVersion) {
				throw new HttpException("It appears that your Caskmaster Version matches the target version as specified in update.xml");
			}
		}

	}

	public function displayUpgradeSteps() {
		if(empty($this->update['target_version'])) {
			throw new HttpException("XML Error: The property target_version is not defined.");
		}

		$runUpgrade = false;
		if(!empty($_GET['run']) && $_GET['run'] == 'true') {
			$runUpgrade = true;
		}
		$this->target_version = $this->update['target_version'];

		$html = "";
		$currentVersion = $_SERVER['app']->get("caskmaster.version");
		$html = "<h2>Update Caskmaster From Version " . $currentVersion . " to " .$this->target_version . "</h2>";
		$html .= $this->loopThroughSteps($runUpgrade);
		if(!$runUpgrade) {
			$html .= "<p><a href=\"?run=true\" class=\"btn btn-warning btn-lg\">Run Upgrade</a></p>";
		} else {
			$html .= "<h2>Upgrade Complete</h2><p>You may need to execute any applicable SQL separately.</p>";
		}
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
					$this->updateConfig($from,$to);
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
				throw new HttpException("The desired branch is not recognised. Please check the update.xml file.");
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

    private function updateConfig($option,$value) {

    	$configLocation = $_SERVER['DOCUMENT_ROOT'] . '/framework/misc/config/config.php';
    	$replaceString = '$app->set("'. $option . '",';

    	$file = file($configLocation);
		$Lines = array();
		foreach($file as $line) {
			if($this->contains($replaceString,$line)) {
				$replace = str_replace($_SERVER['app']->get($option), $value, $line);
				$Lines[] = $replace;
			} else {
				$Lines[] = $line;
			}
		}

		$NewContent = implode("", $Lines);

		file_put_contents($configLocation, $NewContent);
		return true;
    }

    private function contains($needle, $haystack) {
    	return strpos($haystack, $needle) !== false;
	}

	public function fetchLatestUpdateXml($branch="master") {
		$latestUpdateXml = "https://raw.githubusercontent.com/loftyd/caskmaster2/$branch/framework/misc/update/update.xml";
		$ourUpdateXml = $configLocation = $_SERVER['DOCUMENT_ROOT'] . "/framework/misc/update/update.xml";
		clearstatcache();
		if(file_exists($latestUpdateXml) || $this->file_contents_exist($latestUpdateXml)) {
			try {
				$contents = file_get_contents($latestUpdateXml);
				file_put_contents($ourUpdateXml, $contents);
				return true;
			} catch(Exception $e) {
				throw new HttpException("Cannot find latest update.xml");
			}
		} else {
			return false;
		}


	}

	public function file_contents_exist($url, $response_code = 200) {
    	$headers = get_headers($url);

	    if (substr($headers[0], 9, 3) == $response_code)
	    {
	        return TRUE;
	    }
	    else
	    {
	        return FALSE;
	    }
	}

}