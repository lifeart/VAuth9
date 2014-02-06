<?php

// тестовый файл, не имеющий привязки к cms ( вместо CMS используем возможности VAuth )

$compability = array('8.2','8.3','8.5','8.7','8.9','9.0','9.2','9.3','9.5','9.6','9.7','9.9','10.0','10.1');

$cms = array(
	'config'		=> array(
		'url'				=> 'http://vauth.ru',
		'title'				=> 'vAuth 9 test page',
		'offline'			=> false,
		'admin'			=> '/admin',
		'charset'		=> 'utf-8',
		'img_width'	=> 200,
		'modrewrite'	=> true,
		'img_quality'	=> 86
	),
	'db'			=> array(
		'user'			=> 'test',
		'host'				=> 'localhost',
		'pass'			=> 'test',
		'name'			=> 'test'
	),
	'tables'		=> array(
		'user'			=> array(
			'tb_name'		=> 'vauth_users',
			'tb_id'			=> 'user_id',
			'tb_login'		=> 'name',
			'tb_email'		=> 'email'
		)
	),
	'dir'					=> false,
	'avatar'				=> '/avatar',
	'url'					=> 'http://vk.com/vauth',
	'name'				=> 'vAuth',
	'version'			=> 9,
	'fullname'			=> 'Virtual Auth'
);