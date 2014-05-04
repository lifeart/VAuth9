<?php

	namespace Vauth;
	
	define('vauthDIR',dirname(__FILE__));
	define('vauthLang','russian');
	
	class vAuth {
	
		var $cms = false;
		var $network = false;
		var $user = false;
		var $db = false;
		var $config = false;
		var $lang = false;
		var $api = false;
		
		function __construct($params=false) {
			$this->_loadConfig();
			if (!isset($params['cms'])) $params['cms'] = 'self';
			$this->_loadEngine($params['cms']);
			$this->_loadDatabase();
			$this->_install();
			$this->_loadLanguage();
			if (isset($params['api']))
				$this->_loadAPI($params['api']);
			if (isset($params['network']))
				$this->_loadNetwork($params['network']);
			else $this->_loadNetwork();
			// if (isset($params['network']))
				$this->_loadUser();
				
			$this->_configureNetwork($this->cms->config);
			$this->_configureUser($this->cms->config);
			$this->_configureEngine($this->db);
			
			$this->_build();
			$this->_test();
		}
		
		private function _configureEngine($db) {
			$this->cms->db = $db;
			$this->cms->api->db = $db;
		}
		
		private function _configureUser($params=false) {
		
		}
		
		private function _configureNetwork($config=false) {
			if ($this->network != false) {
				$this->network->config = $config;
				if ($this->network->methods!=false) {
					$stuff_db = $this->db->exec("SELECT * FROM {$this->config->db->table['providers']}  WHERE provider = '{$this->network->methods->prefix_big}' ORDER BY  `date` ASC LIMIT 0, 10");
					if (count($stuff_db)) {
						$this->network->methods->config = $stuff_db[0];
						// print_r($this->network->methods->config);
					} else {
						die('No network config');
					}
				}
				else 
					die('No network file');
			}
		}
		
		private function _build() {
			// print_r($this->db);
			// $this->user->db = $this->db;
			// $this->cms->db = $this->db;
		}
	
		public function _install() {
			if (!$this->_checkInstallation()) {
				$this->_installTable();
			}
		}
		
		private function _cleanName($name='') {
			return strtolower(trim($name));
		}
		
		private function _loadLanguage($localization='russian') {
		
			$fname = vauthDIR . '/langfiles/'.vauthLang.'.php';
			if (file_exists($fname)) include_once($fname);
			$this->lang = new vLanguage();
		
		}
		
		private function _loadDatabase(){
		
			$host = $this->cms->config['db']['host'];
			$port = 3306;
			$dbname = $this->cms->config['db']['name'];
			$username = $this->cms->config['db']['user'];
			$password = $this->cms->config['db']['pass'];
			
			try {
				$this->db = new \DB\SQL('mysql:host='.$host.';port='.$port.';dbname='.$dbname,$username,$password);
			} catch (Exception $e) {
				echo 'iohoih';
			}
		}
		
		private function _loadConfig($name='Vauth\vConfig') {
			$this->config = new $name();
		}
		
		private function _installTable() {
		
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// создаём таблицу-связку для текущей CMS
			// id - ID записи о пользователе
			// auth_id - ID пользователя в VAuth
			// cms_id - ID этого-же пользователя в CMS
			// cms_name - Название CMS (для одновременной авторизации в нескольких CMS)
			// password - хэш пароля для авторизации пользователя в текущей CMS
			// date - дата для определения времени изменения
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$this->db->exec("CREATE TABLE IF NOT EXISTS {$this->config->db->table['userlink']} (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, auth_id INT NOT NULL, cms_id INT NOT NULL,  cms_name VARCHAR( 20 ) NULL, password VARCHAR( 120 ) NOT NULL , date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)");

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// создаём таблицу с данными о авторизации пользователей через соц.сети
			// id - ID записи
			// auth_id - ID пользователя в VAuth
			// uid - ID пользователя в социальной сети
			// token - ключ авторизации пользователя в социальной сети
			// profile_link - ссылка на профиль пользователя в этой социальной сети
			// date - дата для определения времени изменения
			// registered - флаг, который говорит что пользователь зарегистрирован через эту сеть
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$this->db->exec("CREATE TABLE IF NOT EXISTS {$this->config->db->table['networks']} (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, auth_id INT NOT NULL, uid VARCHAR( 120 ) NULL, token VARCHAR( 120 ) NOT NULL , profile_link VARCHAR( 120 ) NOT NULL, date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, registered BOOL NOT NULL DEFAULT  '0')");

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// создаём таблицу с данными о друзьях пользователя
			// id - ID записи
			// friend_one - одна часть дружбы
			// friend_two - другая часть дружбы
			// friendship - есть ли дружба? (по умолчанию есть, раз мы добавили эту запись)
			// date - дата для определения времени изменения
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			

			$this->db->exec("CREATE TABLE IF NOT EXISTS {$this->config->db->table['friends']} (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, friend_one INT NOT NULL, friend_two INT NOT NULL, friendship BOOL NOT NULL DEFAULT  '1', date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)");
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// создаём таблицу с настройками модуля
			// id - ID записи
			// param - параметр
			// value - значение
			// version - версия модуля
			// date - дата для определения времени изменения
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			

			$this->db->exec("CREATE TABLE IF NOT EXISTS {$this->config->db->table['config']} (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, param VARCHAR( 240 ) NULL, value VARCHAR( 240 ) NULL, version INT NULL, date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)");			

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// создаём таблицу с настройками провайдера
			// id - ID записи
			// alias - человекопонятное название
			// provider - поставщик авторизации
			// app_secret - значение
			// app_key - значение
			// app_id - значение
			// version - значение
			// date - дата для определения времени изменения
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			
			$this->db->exec("CREATE TABLE IF NOT EXISTS {$this->config->db->table['providers']} (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, alias VARCHAR( 240 ) NULL, provider VARCHAR( 240 ) NULL, app_secret VARCHAR( 240 ) NULL, app_key VARCHAR( 240 ) NULL, app_id VARCHAR( 240 ) NULL, version INT NULL, date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)");			

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// создаём таблицу пользователей для дефолтной CMS self
			// id - ID юзера
			// login - логин в CMS
			// password - пароль в CMS
			// email - почта в CMS
			// firstname - имя
			// lastname - фамилия
			// avatar - картинка
			// date - дата регистрации
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			$this->db->exec("CREATE TABLE IF NOT EXISTS {$this->config->db->table['self']} (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, login VARCHAR( 240 ) NULL, password VARCHAR( 240 ) NULL, e VARCHAR( 240 ) NULL, firstname VARCHAR( 240 ) NULL, lastname VARCHAR( 240 ) NULL, avatar VARCHAR( 240 ) NULL, date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)");			
			


		}
		
		private function _checkInstallation() {
		
			$install_flag = 'install.lock';
			$path = vauthDIR . '/'.$this->config->flagdir.'/'.$install_flag;
			if (!file_exists($path)) {
				file_put_contents($path,$this->config->version);
				return false;
			}
			return true;
		}
		
		private function _loadEngine($cms='self'){
			$this->cms = new vEngine($cms);
		}
		
		private function _loadNetwork($network=false){
			// echo $network;
			if ($network != false) {
				$config = array(
					'network' => $network,
					'db' => $this->db
				);
				$this->network = new vNetwork($config);
			}
		}

		private function _loadUser($class='\\Vauth\\vUser') {
			$constructor = array(
				'api' => $this->api,
				'network' => $this->network,
				'cms' => $this->cms,
				'db' => $this->db
			);
			// echo '<pre>';
			// print_r(get_declared_classes());
			// $reflection = new \ReflectionClass($class);
			// $this->user = $reflection->newInstanceArgs($constructor);
			$this->user = new vUser($constructor);
		}
	
		private function _test($debug=true) {
			return true;
			$error = '';
			$error .= $this->cms->error();
			$error .= $this->network->error();
			$error .= $this->user->error();
			// $error .= $this->db->error();
			// $error .= $this->config->error();
			// $error .= $this->lang->error();
			// $error .= $this->api->error();
			if ($error == '') return true;
			elseif ($debug == false) return false;
			else echo $error;
		}
		
		private function _loadAPI($class) {
			$this->api = $class;
		}
	}
	
	// $f3->va = new vAuth('cms'=>'dle','network'=>'vk','api'=>$f3);
	// $f3->va->user->auth();