<?php

	namespace Vauth;
	
	
	class vDatabaseConfig {
	
		var $table = false;
		var $names = false;
		
		function __construct() {
			$this->_loadTableConfig();
		}
		
		private function _loadTableConfig() {
			
			$prefix = 'v_';
			
			$config = array(
				'userlink'		=> $prefix.'userlink',
				'networks'		=> $prefix.'userdatasocial',
				'config'		=> $prefix.'config',
				'friends'			=> $prefix.'friends'
			);
			
			$config_2 = array();
			
			$this->table = $config;
		
		}
	}