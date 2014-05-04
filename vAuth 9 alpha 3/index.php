<?php

$f3=require('lib/base.php');

$f3->set('DEBUG',1);

if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

$f3->config('config.ini');

$classes=array(
	 'Vauth'=>array(
					'vAuth'
				)
);
$f3->set('classes',$classes);

$f3->route('GET /',
	function($f3) {
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

$f3->route('GET /cms',function($f3,$params){
	$config = array('cms'=>'self');
	$f3->va = new Vauth\vAuth($config);
	echo '<pre>';
	if ($f3->va->cms->api->checkAuth($f3)) print_r($f3->get('SESSION'));
	else echo 'noauth';
});


$f3->route('GET /go/*',
	function($f3,$params) {

		if (strpos($params[1],'?') !== false) {
			$params[1] = substr($params[1],0,strpos($params[1],'?'));
		}

		$network = preg_replace("/[^a-z]+/", "",strtolower(trim($params[1])));
		$config = array('cms'=>'self','network'=>$network);
		$f3->va = new Vauth\vAuth($config);

		if (!$f3->get('SESSION.autorization.'.$network)) {
			$auth_result = $f3->va->network->auth($f3->get('GET'));
			if ($auth_result !== true) 
				$f3->reroute($auth_result);
			else 
				$f3->set('SESSION.autorization.'.$network,$f3->va->network->methods->getAutorizationParams());
		} else {
			$f3->va->network->methods->setAutorizationParams($f3->get('SESSION.autorization.'.$network));
		}
		
		
		if ($f3->va->cms->api->checkAuth($f3)==false) {
			// если текущий пользователь не авторизован в движке
			if ($f3->va->cms->api->login($f3) == false) {
				// если мы не смогли его аторизовать
				if($f3->va->cms->api->register()==false) {
					// если мы не смогли его зарегистрировать, то жопач
				} else {
					// если смогли его зарегистрировать
					// !!!!!!!!! написать сопряжение с бд модуля
					if ($f3->va->cms->api->login($f3) == false) {
						die('ooj');
					}
					
				}
			} else {
				// тут делаем что-то если пользователь успешно залогинен в движке
			}
		} else {
			// если пользователь авторизирован в движке, то подключаем социальную сеть к нему)
			// $f3->va->api->connect($f3);
		}
		
		$cms_register = $f3->va->cms->api->register(array('login'=>'Alex','password'=>'jonyMo','email'=>'mys@mail.to'));
		if ($cms_register != false) {
			
		} else echo 'regerror';

		$profile = $f3->va->network->get('profile');
		$f3->set('profile',$profile);
		$f3->set('content','profile.htm');
		echo View::instance()->render('layout.htm');		
		
	}
);

$f3->route('GET /admin',
	function($f3,$params) {
		$f3->set('content','admin.htm');
		echo View::instance()->render('layout.htm');
	}
);
$f3->run();