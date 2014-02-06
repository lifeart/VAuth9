<?php

	namespace Vauth;
	
	class vUser {
	
		var $id = false;
		var $info = false;
		var $network = false;
		var $api = false;
		var $error = array();
		
		function __construct($params) {
			$this->network = $params['network'];
			$this->api = $params['api'];
		}
		
		function error() {
			return implode("\r\n",$this->error);
		}
		
		function newError($text='') {
			$this->error[] = $text;
		}
		
		function _session($key,$value='{}') {
			if ($value == '{}') return $_SESSION[$key];
			else $_SESSION[$key] = $value;
		}
		
		private function _setUser($id=false) {
			if ($id == false) $this->id = $this->_getCurrentUserId();
		}
		
		private function _getCurrentUserId() {
			if (!empty($this->_session('vauth_id'))) return $this->_session('vauth_id');
			else $this->auth();
		}
	
		public function auth($network=false) {
			if (!$network&&!$this->network) {
				$this->api->render('auth.htm');
			} else {
				if ($this->checkNetworkAuth) {
					if ($this->cms->checkAuth()) 
						$this->connect();
					else $this->register();
					$this->login();
				}
			}
		}
	
	}