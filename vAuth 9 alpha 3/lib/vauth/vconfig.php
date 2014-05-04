<?php

	namespace Vauth;
	
	class vConfig {
	
		var $db = false;
		var $version = '9alpha_1';
		var $flagdir = 'flags';
		
		function __construct($params=false) {
			$this->_loadDatabaseConfig();
		
		}
		
		private function _loadDatabaseConfig($class='Vauth\vDatabaseConfig') {
			$this->db = new $class();
		}
	
	}