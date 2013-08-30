<?php
	
	include_once("settings/script_settings.php");

	$_ref = @$_REQUEST['ref'];
	$_code = @$_REQUEST['code'];
	$_network = @$_REQUEST['network'];
	$_SesNetwork = @$_SESSION['v_network'];
	
	if (!empty($_code)) {
	
		if (empty($_network)) {
			if (!empty($_SesNetwork)
				$_network = $_SesNetwork;
			else newError('No network name',1);
		}
		
		$vauth = new Vauth($_network);
		
	
	} else {
	
		if (empty($_SesNetwork))
			Vauth:authFrom($_network);
	
	}