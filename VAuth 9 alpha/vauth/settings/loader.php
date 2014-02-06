<?php

	define ('vauthCMS','dle');
	define ('vauthLang','russian');
	
	// тут определяем корневую парку модуля
	define	('vauthDIR', substr(dirname (__FILE__),0,strpos( dirname ( __FILE__ ),"settings" )-1) );
	
	include_once(vauthDIR . '/classes/'.vauthCMS.'/cms.controller.php');
	include_once(vauthDIR . '/classes/vauth/main.class.php');
	include_once(vauthDIR . '/langfiels/'.vauthLang.'.php');
	
	
	$vauth = new Vauth();
	$vauth->cms = new VAuthCMS();
	$vauth->db = $vauth->cms->db;
	$vauth->user = new VauthUser();
	$vauth->language = new VauthLanguage();
	$vauth->network = new VauthNetwork();
	
	$_ref = @$_REQUEST['ref'];
	$_code = @$_REQUEST['code'];
	$_network = @$_REQUEST['network'];
	$_ses_network = @$_SESSION['v_network'];
	
	// $vauth = new Vauth($_network,$_ses_network);
	
	// $vauth->auth();
	// $vauth->controller();