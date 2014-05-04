<?php

	namespace Vauth;
	
	class vEngineMethods {
	
		var $db = false;
		var $config = false;
		
		function __construct($params=false) {
			if (isset($params['db'])) $this->db = $params['db'];
			if (isset($params['config'])) $this->config = $params['config'];
		}
	
		function checkAuth($f3) {
			if ($f3->get('SESSION.self_cms_logged')==true) return true;
			else return false;
		}
	
		function autorize($params) {
			return true;
		}
		
		function changepass($params) {
			return true;
		}
		
		function login($f3) {
			// $this->db->exec();
			$f3->set('SESSION.self_cms_logged',true);
			return true;
			// $f3->set('SESSION.self_cms_uid',true);
		}
		
		function register($params=false) {
			
			if (empty($params['login'])) return false;
			if (empty($params['email'])) return false;
			if (empty($params['password'])) return false;
			
			$usertable = $this->config['tables']['user']['table_name'];
			
			$user_id = $this->config['tables']['user']['user_id'];
			$user_login = $this->config['tables']['user']['user_login'];
			$user_email = $this->config['tables']['user']['user_email'];
			$user_password = $this->config['tables']['user']['user_password'];
			
			$where = "WHERE `{$user_login}` = '{$params['login']}' or `{$user_email}` = '{$params['email']}'";
			
			$stuff_db = $this->db->exec("SELECT * FROM `{$usertable}` {$where} LIMIT 0,1");
			
			$new_user_id = false;
			
			if (!count($stuff_db)) {
			
				$result = $this->db->exec("INSERT INTO `{$usertable}` (`{$user_login}`,`{$user_email}`,`{$user_password}`) VALUES ('{$params['login']}','{$params['email']}','{$params['password']}')");

				$new_user_id = $this->db->lastInsertId();
			
			}
			
			return $new_user_id;
		}
	}