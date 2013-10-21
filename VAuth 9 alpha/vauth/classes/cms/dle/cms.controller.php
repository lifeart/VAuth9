<?php
	
	include_once('api.class.php');
	
	class VauthCMS {
	
		var $description = 'DataLifeEngine CMS VAuth Class';
		var $name = 'DataLife Engine';
		var $compability = '> 8.2';
		var $version = '0.1';
		var $user = false;
		
		function __cunstruct() {
		
			$this->user = new vatuhCmsUser();
			$this->db = new vauthCmsDb();
		
		}
	
	}
	
	
	class vatuhCmsUser {
		
		function getByID($id=false) {}
		function updateUserLogin($id,$login) {}
		function updateUserEmail($id,$mail) {}
		function updateUserPassword($id,$password) {}
		
		function info() {}
		function get() {}
		function set() {}
		function remove() {}
		function login($data) {}
		function register($data) {}
		function check();
	
	}
	
	class vauthCmsDb {
	
	
	
	}