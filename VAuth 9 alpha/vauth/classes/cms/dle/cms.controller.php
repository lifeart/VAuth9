<?php
	
	class VauthCMS {
	
		var $description = 'DataLifeEngine CMS VAuth Class';
		var $name = 'DataLife Engine';
		var $compability = '> 8.2';
		var $version = '0.1';
		var $user = new VatuhCMSUser();
	
	}
	
	
	class VatuhCMSUser {
		
		
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