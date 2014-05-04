<?php

	class VAUTH {
	
		var $className = 'VAUTH';
		
		// подключаем модель работы с пользователями
		var $user = false;
		// подключаем модель работы с CMS
		var $cms = false;
		// var $vauth = false;
		var $network = false;
		var $error = false;
		
		var $uid = false;
		var $social = false;
		
		// при создании модели нам нужно указать социальную сеть, с которой мы работаем
		function __construct($network=false) {
			// $this->network = new vauthNetwork($network);
			// $this->user = new vauthUserModel();
			// $this->cms = new vauthCMSModel();
			// $this->error = new vauthError();
		}
		
		function setNetwork($network=false) {
			$this->network = new vauthNetwork($network);
		}
		
		function auth() {
			$this->cms->auth($this->user);
			$this->network->auth($this->user);
		}
		function authStatus() {
			$flag_1 = $this->network->auth();
			$flag_2 = $this->cms->auth();
			if ($flag_1 && $flag_2) return true;
			else return false;
		}		
		function register() {
			$this->social->getInfoById($this->uid);
			$cmsRegResult = $this->cms->register($this->social->info);
			$this->vauth_register($cmsRegResult);
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
		// контроллер удаления непровославных записей
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