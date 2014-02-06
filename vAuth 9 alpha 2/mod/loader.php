<?php

	define ('vauthCMS','dle');
	define ('vauthLang','russian');
	
	// тут определяем корневую парку модуля
	define	('vauthDIR', dirname (__FILE__));
	$loadpath = '/classes/vauth';
	
	// $instances = array(
		// 'cms','network','user','vauth'
	// );
	
	// foreach ($instances as $instance) {
		// include_once(vauthDIR . $loadpath.'/model/'.$instance.'.php');
		// include_once(vauthDIR . $loadpath.'/controller/'.$instance.'.php');
	// }

	include_once(vauthDIR . '/classes/cms/'.vauthCMS.'/cms.controller.php');
	include_once(vauthDIR . $loadpath.'/model/cms.php');
	include_once(vauthDIR . $loadpath.'/model/network.php');
	include_once(vauthDIR . $loadpath.'/model/user.php');
	
	include_once(vauthDIR . $loadpath.'/controller/main.php');
	include_once(vauthDIR . $loadpath.'/controller/user.php');
	include_once(vauthDIR . '/classes/langfiles/'.vauthLang.'.php');
	include_once(vauthDIR . $loadpath.'/error.php');
	
	
	$vauth = new Vauth();
	$vauth->cms = new VAuthCMS();
	$vauth->db = $vauth->cms->db;
	$vauth->user = new VAuthUser();
	$vauth->language = new VauthLanguage();
	// $vauth->network = new vauthNetwork();
	
	$_ref = @$_REQUEST['ref'];
	$_code = @$_REQUEST['code'];
	$_network = @$_REQUEST['network'];
	$_ses_network = @$_SESSION['v_network'];
	
	$vauth->_ref = $_ref;
	$vauth->_code = $_code;
	$vauth->_network = $_network;
	$vauth->_ses_network = $_ses_network;
	
	return $vauth;
	// $vauth = new Vauth($_network,$_ses_network);
	
	// $vauth->auth();
	// $vauth->controller();