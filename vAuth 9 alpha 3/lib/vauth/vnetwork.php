<?php

	namespace Vauth;
	
	class vNetwork {
		
		var $methods = false;
		// var $network_name = false;
		var $network_file_postfix = '_functions.php';
		var $network_file_prefix = '';
		// var $func_path = '../networks';
		var $db = false;
		var $config = array();
		var $error = array();
	
		function __construct($config=false) {
			$this->db = $config['db'];
			if ($config['network']!=false) $this->_loadNetwork($config['network']);	
			else $this->newError('empty network model');
		}
		
		function error() {
			return implode("\r\n",$this->error);
		}
		
		function newError($text='') {
			$this->error[] = $text;
		}
		
		private function _loadNetwork($network) {
			if (!empty($network) && ctype_alpha($network) == true) {
				$network = trim(mb_strtolower($network));
				$fname = vauthDIR . '/networks/' .$network.'/'. $this->network_file_prefix . $network . $this->network_file_postfix;
				if (file_exists($fname)) {
					require_once($fname);
					$config = array(
						'db' => $this->db
					);
					$this->methods = new socialModel($config);
				} else {
					$this->methods = false;
					$this->newError('no network function file');
				}
			}  else $this->newError('empty nerwork name');
		
		}
		
		function get($query,$params=false) {
			$method = 'get'.ucfirst(strtolower($query));
			if (method_exists($this->methods,$method)) 
				return $this->methods->$method($params);
			else $this->newError('this network has no method',1); 
		}
		
		function auth($params=false) {
			
			return $this->methods->authControl($params,$this->config);
			// if (!$this->methods->token)
				// return $this->methods->authFirst($this->config);
			// elseif  ($this->methods->code)
				// return $this->methods->authSecond();
			// else return true;
		}
	
	}