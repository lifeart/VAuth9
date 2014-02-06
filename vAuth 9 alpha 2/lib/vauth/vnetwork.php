<?php

	namespace Vauth;
	
	class vNetwork {
		
		var $network = false;
		// var $network_name = false;
		var $network_file_postfix = '_functions.php';
		var $network_file_prefix = '';
		// var $func_path = '../networks';
		var $error = array();
	
		function __construct($network=false) {
			if ($network!=false) $this->_loadNetwork($network);	
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
				$fname = vauthDIR . '/networks/' . $this->network_file_prefix . $network . $this->network_file_postfix;
				if (file_exists($fname)) {
					require_once($fname);
					$this->network = new socialModel();
				} else $this->newError('no network function file');
			}  else $this->newError('empty nerwork name');
		
		}
		
		function get($query,$params=false) {
			$method = 'get'.ucfirst(strtolower($query));
			if (method_exists($this->network,$method)) 
				return $this->network->$method($params);
			else $this->newError('this network has no method',1); 
		}
		
		function auth($params=false) {
			if (!$this->network->token)
				return $this->network->authFirst();
			elseif  ($this->network->code)
				return $this->network->authSecond();
			else return true;
		}
	
	}