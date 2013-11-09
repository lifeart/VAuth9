<?php

// dle_vauth_users
// - id
// - user_id
// - password_hash

// dle_vauth_userdata
// - id
// - user_id
// - name
// - data
// - network_id (this is id in dle_vauth_user_networks)

// dle_vauth_user_networks
// - id
// - uid
// - token
// - name
// - upd_time (timestamp)
// - auth_date (timestamp)
// - registered (FALSE)


// dle_vauth_user_friends
// - id
// - user_id
// - network_id  (this is id in dle_vauth_user_networks)
// - network_name
// - friend_uid
// - friend_dle

// select * from _vauth_user_friends where user_id = {$id} and friend_dle != '0'

// select * from dle_vauth_user_networks where uid in ('$uid_1','$uid_2') and network = "vk"

	class vauthNetworkModel {
	
		var $className = 'vauthNetworkModel';
		var $network_name = false;
		var $network_file_postfix = '_functions.php';
		var $network_file_prefix = '';
		var $func_path = '../networks';
		var $code = false;
		// var $autentificated = false;

		function __construct($network,$code=false) {
			$this->_loadNetwork($network,$code=false);
		}
		
		function _loadNetwork($network,$code=false) {
			$network = trim(mb_strtolower($network));
			if (!empty($network) && ctype_alpha($network) == true) {
				$fname = $this->func_path . '/' . $this->network_file_prefix . $network . $this->network_file_postfix;
				if (file_exists($fname) {
					require_once($fname);
					$this->network = new Network();
					$this->network_name = $network;
				} else newError('no network function file',1);
			}  else newError('empty nerwork name',1);
			if ($code != false) {
				$this->code = $code;
			}
			return true;
		}
		
		function auth() {
			if (!$this->network->token) return $this->network->authFirst();
			elseif  ($this->network->code) return $this->network->authSecond();
			else return true;
		}
		
		function userInfo() {
			if ($this->auth()) {
				return $this->network->info();
			} else newError('no user network autorization',1);
		}
		
		private function userFriends() {
			if ($this->auth()) {
				return $this->network->friends();
			}		
		}
	}