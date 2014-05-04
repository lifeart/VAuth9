<?php

// ориентируемся по глобальным переменным и путям
if (!defined('ROOT_DIR'))				define	('ROOT_DIR',substr(dirname (__FILE__),0,strpos( dirname ( __FILE__ ),"engine" )-1));
if (!defined('ENGINE_DIR'))			define	('ENGINE_DIR',ROOT_DIR . '/engine'	);
// загружаем настройки DLE
include_once(ENGINE_DIR.'/data/config.php');
include_once(ENGINE_DIR.'/data/dbconfig.php');

$compability = array('8.2','8.3','8.5','8.7','8.9','9.0','9.2','9.3','9.5','9.6','9.7','9.9','10.0','10.1');

$cms = array(
	'config'		=> array(
		'url'				=> $config['http_home_url'],
		'title'				=> $config['home_title'],
		'offline'			=> $config['site_offline'],
		'admin'			=> $config['admin_path'],
		'charset'		=> $config['charset'],
		'img_width'	=> $config['tag_img_width'],
		'modrewrite'	=> $config['allow_alt_url'],
		'img_quality'	=> $config['jpeg_quality']
	),
	'db'			=> array(
		'user'			=> DBUSER,
		'host'				=> DBHOST,
		'pass'			=> DBPASS,
		'name'			=> DBNAME,
		'prefix'			=> PREFIX,
		'collate'			=> COLLATE,
		'uprefix'			=> USERPREFIX
	),
	'tables'		=> array(
		'user'			=> array(
			'tb_name'		=> USERPREFIX.'_users',
			'tb_id'			=> 'user_id',
			'tb_login'		=> 'name',
			'tb_email'		=> 'email'
		)
	),
	'dir'					=> ROOT_DIR,
	'avatar'				=> ROOT_DIR,
	'url'					=> 'http://dle-news.ru',
	'name'				=> 'dle',
	'version'			=> $config['version_id'],
	'fullname'			=> 'DataLife Engine'
);

unset($config);
unset($db);

// if (!in_array($cms['version'],$compability)) {
	// echo 'Нет информации о совместимости версии CMS';
// }