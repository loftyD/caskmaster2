<?php
/**
 *  admin/home.php
 *  AdminController::actionHome() view
 */
?>
	<div class="container">
        <h1>Dashboard</h1>
	        <?php if($latest > $_SERVER['app']->options()->get("caskmaster.version")) { ?>
	        <div class="jumbotron">
	        	<div class="alert alert-danger">
	        		<p>Your Caskmaster Version is <strong><?=$_SERVER['app']->options()->get("caskmaster.version");?></strong>. The latest version is <strong><?=$latest;?></strong><br />
	        		<strong>Your Caskmaster Installation needs to be updated!</strong><br />
	        		<a class="btn btn-warning btn-sm" href="/admin/update" role="button">Update &raquo;</a></p>
	        	</div>
	        </div>
	        <?php } ?>

	    <?=$assigned_companies;?>
        


    </div>