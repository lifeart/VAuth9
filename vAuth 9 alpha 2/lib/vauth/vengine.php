<?php

	namespace Vauth;

	class vEngine {
	
		var $cms = false;
		var $config = false;
		var $error = array();
		
		function __construct($cms=false) {
			$this->_loadEngine($cms);
		}
	
		function error() {
			return implode("\r\n",$this->error);
		}
		
		function newError($text='') {
			$this->error[] = $text;
		}
		
		private function _cmsFilter($cms) {
			$cms = strtolower(trim($cms));
			switch($cms) {
				case 'data life engine': $cms = 'dle'; break;
				case 'datalife engine': $cms = 'dle'; break;
				case 'datalifeengine': $cms = 'dle'; break;
				case 'wordpress': $cms = 'wp'; break;
				default: break;
			}
			return $cms;
		}
		
		private function _loadEngine($cms=false) {
			if (!empty($cms) && ctype_alpha($cms) == true) {
				$cms = $this->_cmsFilter($cms);
				$adapter = vauthDIR . '/cms/' . $cms . '/adapter.php';
				$api = vauthDIR . '/cms/' . $cms . '/api.php';
				if (file_exists($adapter)) {
					require_once($adapter);
					$this->config = $cms;
				} else $this->newError('no cms config file');
				
				if (file_exists($api)) {
					include_once($api);
					$this->api = new vEngineMethods();
				} else $this->newError('no cms api file');
				
			}  else $this->newError('empty nerwork name');
		}
		
		
	}