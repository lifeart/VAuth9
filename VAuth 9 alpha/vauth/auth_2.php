<?php
	
	include_once("settings/script_settings.php");

	$_ref = @$_REQUEST['ref'];
	$_code = @$_REQUEST['code'];
	$_network = @$_REQUEST['network'];
	$_ses_network = @$_SESSION['v_network'];
	
	$vauth = new Vauth($_network,$_ses_network);
	
	$vauth->auth();
	$vauth->controller();
	