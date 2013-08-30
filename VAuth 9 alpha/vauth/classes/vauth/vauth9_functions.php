<?php

class vauthUser {

	function  get($id=0) {
	
		$id = abs(intval($id));
		
		if ($id = 0) return false;
		
		$query_1 = $this->db->load_table('dle_vauth_userdata', "*","user_id = '$id' ", false);
		
		$userdata->hash = $query_1['password_hash'];
		$userdata->bdate = $query_1['bdate'];
		$userdata->phone = $query_1['phone'];
		$userdata->friends = $query_1['userfriends'];
		$userdata->updtime = $query_1['update_time'];
		
		$social_info = $this->db->load_table('dle_vauth_userinfo', "*","user_id = '$id' ", true);
		
		foeach ($social_info as $k=>$v) {
			
			$net = array();
		
			$net->id = $v['network_id'];
			$net->hash = $v['network_hash'];
			$net->link = $v['network_link'];
			$net->email = $v['network_email'];
			
			$userdata->social[] = $net;
			
		}
		
		return json_encode($userdata);
		
	}
	
	function connect($array,$user_id) {
	
		if (!isset($array['network_name'])) return false;
		
		$info = $this->getVauthUserinfo($user_id);
	
		if ($settings['multiaccount'] = true) {
		
			$network_name = $array['network_name'];
			$network_id = $array['network_id'];
			$network_hash = $array['network_hash'];
			$network_link = $array['network_link'];
			$network_email = $array['network_email'];
		
			$this->db->query( "insert into dle_vauth_userdata
			
				(user_id, network_name, network_id, network_hash, network_link, network_email)
				VALUES 
				('$user_id', '$network_name', '$network_id', '$network_hash', '$network_link', '$network_email') " );
			
			if ($insert_id = $this->db->insert_id()) {
		
				$response['status'] = TRUE;
				$response['insert_id'] = $insert_id;
			
			} else $response['status'] = FALSE;
			
			return json_encode($response);
		
		}
	
	}
	
	function disconnect($user_id,$network_name) {
	
		if ($_SESSION['user_id'] == $user_id) {
	
			if($this->db->query("DELETE FROM dle_vauth_userdata where user_id = '$user_id' and network_name = '$network_name' ")) {
			
				$response['status'] = TRUE;
			
			}
		
			else $response['status'] = FALSE;
			
			return json_encode($response);
		
		}
	
	}

	function auth($network_name,$network_id) {
	
		$network_id = abs(intval($network_id));
	
		$auth_info = $this->db->load_table('dle_vauth_usedata', "*"," network_id = '$network_id' and  network_name = '$network_name' ", false);
	
		if ($auth_info['user_id']) $dle_auth_info = $this->db->load_table('dle_vauth_userinfo', "*"," user_id = '$auth_info[user_id]' ", false);
	
		if ($dle_auth_info['hash'] && $dle_auth_info['user_id']) $vauth_functions->vauthAuth($dle_auth_info['hash'],$dle_auth_info['user_id']);
		
		else return false;
		
	}

}
?>