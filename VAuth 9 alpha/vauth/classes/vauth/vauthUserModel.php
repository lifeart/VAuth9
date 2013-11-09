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

	class vauthUserModel {
	
		var $className = 'vauthUserModel';
		var $cms->db->vauth_users_networks = USERPREFIX."_vauth_user_networks";
		var $cms->db->vauth_users = USERPREFIX."_vauth_users";
		var $user_table = USERPREFIX."_users";
		var $errors = new vauthError();
		
		function __construct($cms=false) {
			if (!$cms) $this->errors->logError('Невозможно создать модель пользователя, так как не указана CMS');
			$this->cms = new vauthCMS($cms);
		}
		
		private function getUserById($id=false) {	
			$functionName = 'getUserById';
			if (is_numeric($id) && $id > 0) {
				$resp = array();
				$resp->id= $id;
				
				$superq = "
					
					SELECT {$this->cms->db->user_table}.*,{$this->cms->db->vauth_users}.*,{$this->cms->db->vauth_users_networks}.*
					FROM {$this->cms->db->user_table} 
					LEFT JOIN {$this->cms->db->vauth_users} 
					ON {$this->cms->db->user_table}.user_id={$this->cms->db->vauth_users}.stuff_id
					LEFT JOIN {$this->cms->db->vauth_users_networks}
					ON {$this->cms->db->user_table}.user_id={$this->cms->db->vauth_users_networks}.user_id
					WHERE {$this->cms->db->user_table}.user_id = '$id'
				
				";
				
				$dUserinfo = $this->load_table($this->cms->db->user_table, "*", "user_id = '$id'");
				if (!$dUserinfo) {
					$eParams = array();
					$eParams[] = $id;	
					newError($className,$functionName,'dUserInfo',$eParams);
				} else $resp->dle=$dUserinfo;
				$vUserinfo = $this->load_table($this->cms->db->vauth_users,"*", "user_id = '$id'");
				if (!$vUserinfo) {
					$eParams = array();
					$eParams[] = $id;	
					newError($className,$functionName,'vUserInfo',$eParams);
				} else $resp->vauth=$vUserinfo;
				$vUserNetworks = $this->load_table($this->cms->db->vauth_users_networks,"*", "user_id = '$id'",true);
				if (!$vUserNetworks) {
					$eParams = array();
					$eParams[] = $id;	
					newError($className,$functionName,'vUserNetworks',$eParams);
				} else $resp->networks=$vUserNetworks;
				return $resp;
			} else {
				$eParams = array();
				$eParams[] = $id;	
				newError($className,$functionName,'varValidation',$eParams);
				return false;
			}
		}
		private function setCms ($id,$array) {
			$functionName = 'setCms';
			if (is_numeric($id) && $id>0) {
				$uResult = array();
				foreach ($array as $key=>$value) {
					$uResult[$key] = $this->db->query( "UPDATE " . $this->cms->db->user_table . " SET {$key}='{$value}' WHERE user_id='{$id}'" );
				}
				return $uResult;
			} else {
				newError($className,$functionName,'varValidation',$eParams[]=$id);
				return false;
			}
		}
		private function setVauth ($id,$array) {
			$functionName = 'setVauth';
			if (is_numeric($id) && $id>0) {
				$uResult = array();	
				foreach ($array as $key=>$value) {
					$uResult[$key] = $this->db->query( "UPDATE " . $this->cms->db->vauth_users . " SET {$key}='{$value}' WHERE user_id='{$id}'" );
				}
				return $uResult;
			} else return false;
		}
		private function setNetwork ($id,$array) {
			if (is_numeric($id) && $id>0) {
				$uResult = array();
				if (isset( $array->id )) {
					foreach ($array as $key=>$value) {
						try {
							$uResult[$key] = $this->db->query( "UPDATE " . $this->cms->db->vauth_users_networks . " SET {$key}='{$value}' WHERE id='{$array->id}'" );
						} catch (Exception $e) {
							$uResult[$key] = $e;
						}
					}
				} else {
					$this->db->query( "insert into " . $this->cms->db->vauth_users_networks . " (user_id) VALUES ('$id')" );
					$array->id = $this->db->insert_id();
					return $this->setNetwork ($id,$array);
				}
				return $uResult;
			} else return false;		
		}
		private function removeVauthUser($id) {
			if (is_numeric($id) && $id > 0) {
				$this->db->query("DELETE FROM `".$this->cms->db->vauth_users."` where user_id = '{$id}'");
				return true;
			} else return false;
		};
		private function removeVauthUserNetwork($data) {
			$res = array();
			if (isset($data->id)) {
				if (is_numeric($data->id)) {
					$res->id=$this->db->query("DELETE FROM `".$this->cms->db->vauth_users_networks."` where id = '{$data->id}'");
				} elseif ($data->id = '*' && isset($data->user_id)) {
					$res->id=$this->db->query("DELETE FROM `".$this->cms->db->vauth_users_networks."` where user_id = '{$data->user_id}'");
				} else return false;
				return $res->id;
			} elseif (isset($data->ids) && is_array($data->ids)) {
				foreach ($data->ids as $value) {
					$res->ids=$this->removeVauthUserNetwork($value);
				}
				return $res->ids;
			} else return false;
		};
		private function getUserByNetworkNameAndId($network=false,$id=false) {
			if ($network!=false && $id!=false) {
				$vUser = $this->load_table($this->cms->db->vauth_users_networks,"*", "network = '$network' and id='$id'");
				if ($vUser) $user = $this->getUserById($vUser['user_id']);
				else return false;
				if ($user) return $user;
				else return false;
			} else return false;
		}
		private function getUserFriendsByUidsAndNetwork($array,$network=false) {
			if (!$network) return false;
			if (is_array($array))
				$userlist = implode(",", $array);
			else $userlist = $array;
			$result = $this->load_table($this->cms->db->vauth_users_networks, "user_id", "uid in ({$userlist}) and network = '{$network}'",true);
			if ($result) {	
				$friends = array();
				foreach ($result as $value) {
					$friend = load_table($this->cms->db->user_table, "user_id,name,foto,fullname", "user_id = '{$value['user_id']}'");
					if ($friend) $friends[] = $friend;
				}
				if (count($friends)) return $friends;
				else return false;
			} else return false;
		}
		function check($network=false,$id=false) {
			return $this->getUserByNetworkNameAndId($network,$id);
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
					$res->dle = $this->setCms($id,$data->dle);
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