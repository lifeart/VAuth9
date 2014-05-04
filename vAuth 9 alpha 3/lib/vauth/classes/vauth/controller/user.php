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
// - network
// - upd_time (timestamp)
// - auth_date (timestamp)
// - profile_link
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

	class VAuthUser {
	
		var $className = 'VAuthUser';
		var $model = false;
		var $network = false;
		var $uid = false;
		var $social = false;
		
		function __construct($network=false,$uid=false) {
			$this->network=$network;
			$this->uid=$uid;
			$this->model = new vauthUserModel();
		}
		
		function checkDleUserAuth() {
			return true;
		}
		
		function auth() {
			$this->social = new vauthSocial($this->network);
			if ($this->checkDleUserAuth()==true) {
				$this->connect();
			} else {
				$result =  $this->model->check($network,$id);
				if (!$result) {
					$this->register();
				} else $this->login();
			}
		}
		function register() {
			$this->social->getInfoById($this->uid);
			$dleRegResult = $this->dle_register($this->social->info);
			$this->vauth_register($dleRegResult);
			$this->connect($this->social);
			$this->myInfo();
			$this->auth();
		}
		function connect($user_id,$network,$uid,$token,$registered=false) {
			$data = array();
			$data->id = $user_id;
			$data->network->name=$network;
			$data->network->uid=$uid;
			$data->network->token=$uid;
			$data->network->upd_time=time();
			$data->network->auth_date=time();
			$data->network->registered=$registered;
			if ($this->model->check($network,$uid)==false) {
				return $this->model->set($data);
			} else return false;
		}
		function get($data) {
		
			if (is_array($data)) {
				
				$result = array();
			
				foreach ($data as $value) {
			
					$result[] = $this->getUserById($value);
				
				}
				
				return $result;
				
			} else return $this->getUserById($data);
		
		}
		function set($data) {
		
			$functionName = 'set';

			if (!isset($data->id)) {
	
				$eParams = array();
				$eParams[] = $data;	
				newError($className,$functionName,'noUserId',$eParams);	
			
			} else {
			
				$res = array();
			
				$id = $data->id;
				if (isset($data->dle)) {
					$res->dle = $this->setDle($id,$data->dle);
				}
				if (isset($data->vauth)) {
					$res->vauth = $this->setVauth($id,$data->vauth);
				}
				if (isset($data->network)) {
					$res->network = $this->setNetwork($id,$data->network);
				}
				if (isset($data->networks)) {
					
					$nsUpdate = array();
					
					foreach ($data->networks as $key=>$value) {
						$res->networks[] = $this->setNetwork($id,$value);
					}
					
				}

				return $res;
			
			}
		
		}	
		function remove($data) {
			
			$res = array();
			
			if (isset($data->vauth)) {
				$res->vauth = removeVauthUser($data->vauth->user_id);
			}
			
			if (isset($data->network)) {
				
				$res->network = removeVauthUserNetwork($data->network);
			
			}
			
			return $res;
		
		}
		
	}