<?php

$f3=require('lib/base.php');
// $va=require('mod/loader.php');

// $f3->va=$va;

$f3->set('DEBUG',1);
if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

$f3->config('config.ini');
// $classes=array(
	 // 'Vauth'=>array(
					// 'vAuth'
				// )
// );
// $f3->set('classes',$classes);

$f3->route('GET /',
	function($f3) {
		// $classes=array(
			// 'Base'=>
				// array(
					// 'hash',
					// 'json',
					// 'session'
				// )
		// );
		// $f3->set('classes',$classes);
		// $f3->set('content','welcome.htm');
		$f3->set('content','auth.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /auth',
	function($f3) {
		$f3->set('content','auth.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /go/*',
	function($f3,$params) {
		echo '<pre>';
		print_r($f3->va);
		$network = strtolower(trim($params[1]));
		vauthNetwork::load($network);
		$f3->set('content','auth.htm');
		echo View::instance()->render('layout.htm');
	}
);
$f3->route('GET /test',
	function($f3,$params) {
		// $f3->set('AUTOLOAD', 'vauth/');
		// print_r($f3->get('AUTOLOAD'));
		$config = array(
			'cms'=>'self',
			'network'=>'vkontakte'
		);
		$vatuh = new Vauth\vAuth($config);
		echo '<pre>';
		print_r($vatuh);
	}
);
$f3->run();