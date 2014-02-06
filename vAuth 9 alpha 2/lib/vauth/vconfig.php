<?php

	namespace Vauth;
	
	class vConfig {
	
		var $db = false;
		
		function __construct($params=false) {
			$this->_loadDatabaseConfig();
		
		}
		
		private function _loadDatabaseConfig($class='Vauth\vDatabaseConfig') {
			$this->db = new $class();
		}
	
	}