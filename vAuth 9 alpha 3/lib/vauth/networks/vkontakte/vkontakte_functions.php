<?php

	namespace Vauth;

	class socialModel {

		var $api_uri = 'https://api.vk.com/method/';
		var $prefix_small = 'vk';
		var $prefix_big = 'vkontakte';
		var $config = array();
		var $db = false;
		
		var $access_token = false;
		var $uid = false;
		var $expires_in = false;
		
		var $oauth_uri = 'https://oauth.vk.com/';
		var $domain = false;
		
		var $api_version = 5.16;
		var $api_lang = 'ru';
		
		function __construct($params = false) {
			$this->db = $params['db'];
		}
		
		function authControl($params,$config) {
			$this->domain = $config['config']['url'];
			if (isset($params['code'])) return $this->_authSecond($params['code']);
			elseif (!isset($params['error'])) {
				return $this->_authFirst();
			}
		}
		
		private function _authFirst() {
			return $this->_getAuthUri();
		}
		
		private function _authSecond($code=false) {
			$params = array(
				'client_id'=>$this->config['app_id'],
				'client_secret'=>$this->config['app_secret'],
				'redirect_uri'=>$this->_getRedirectUri(),
				'code'=>$code
			);
			$oauth_auth = $this->oauth_uri.'access_token?'.http_build_query($params);
			
			$result = false;
			try {
				$result = @file_get_contents($oauth_auth);
			} catch (Exception $e) {
				print_r($e);
			}
	
			if ($result != false) {
				$userinfo = json_decode($result, TRUE); // * Плучаем секретный хэшкод
				$data = array(
					'access_token' => $userinfo['access_token'],
					'expires_in' => $userinfo['expires_in'],
					'user_id' => $userinfo['user_id']
				);
				$this->access_token = $data['access_token'];
				$this->uid = $data['user_id'];
				$this->expires_in = time()+$data['expires_in'];
				
				return true;
			
			} else {
				die('some auth error');
			}
		}
	
		private function _getRedirectUri() {
			return $this->domain.'go/vkontakte';
		}
		private function _getAuthUri(){
			$r_uri = $this->_getRedirectUri();
			$uri	=	$this->oauth_uri.'authorize?client_id=' . $this->config['app_id'] .'&redirect_uri='.$r_uri.'&response_type=code';
			return $uri;
		}
		
		function setAutorizationParams($array=false) {
			$this->uid = $array['uid'];
			$this->access_token = $array['access_token'];
		}
		
		function getAutorizationParams() {
			return array('uid'=>$this->uid,'access_token'=>$this->access_token);
		}
		
		function getProfile($params=false) {
			if(!isset($params['user_id'])) $user_id = $this->uid;
			else $user_id  = $params['user_id'];
			$query_url = $this->api_uri.'users.get?uids='.$user_id.'&https=1&lang='.$this->api_lang.'&v='.$this->api_version.'&fields=photo_200,photo_200_orig,photo_max_orig,nickname,screen_name,sex,bdate,photo,contacts,activity,relation,activities,interests,movies,tv,books,games,about,quotes,connections,city,country,education&access_token='.$this->access_token;
			$oauth_info = json_decode(file_get_contents($query_url), TRUE);
			return $oauth_info['response'][0];
		}
	
		// ** Функция получения информации из Вконтакте
		function get_oauth_info($oauth) {

			global $vauth_text;
			global $db;
			
			$query_url = $this->api_uri.'users.get?uids='.$oauth['uid'].'&fields=photo_200,photo_200_orig,photo_max_orig,nickname,screen_name,sex,bdate,photo,contacts,activity,relation,activities,interests,movies,tv,books,games,about,quotes,connections,city,country,education&access_token='.$oauth['access_token'];
			
			$oauth_info = json_decode($this->vauth_get_contents($query_url), FALSE);

			if (!$this->get_vk_from_json($oauth_info,'email')) $oauth['email'] =	$oauth['uid'].'@vk.com';
			else $oauth['email']		=	$this->get_vk_from_json($oauth_info,'email');
						
			$oauth['avatar']		=	$this->get_vk_from_json($oauth_info,'photo_200');
			
			
			$oauth['avatars'][]  = $this->get_vk_from_json($oauth_info,'photo_200_orig');
			$oauth['avatars'][]  = $this->get_vk_from_json($oauth_info,'photo_max_orig');
			$oauth['avatars'][]  = $this->get_vk_from_json($oauth_info,'photo');
			
			$oauth['last_name']	=	$this->get_vk_from_json($oauth_info,'last_name');	
			$oauth['first_name']	=	$this->get_vk_from_json($oauth_info,'first_name');
			$oauth['screen_name'] 	=	$this->get_vk_from_json($oauth_info,'screen_name');
			$oauth['nick']		 	=	$oauth['screen_name'];
			$oauth['fullname']		=	$oauth['first_name'] . ' ' . $oauth['last_name']; // Делаем полное имя
			
			$oauth['mobile_phone']	=	$this->get_vk_from_json($oauth_info,'mobile_phone');
			
			#страна проживания пользователя
			$oauth['country']	=	$this->get_vk_from_json($oauth_info,'country');
			$info_country	=	json_decode($this->vauth_get_contents($this->api_uri.'places.getCountryById?cids='.$oauth['country'].'&access_token='.$oauth['access_token']),FALSE);
			$oauth['country']	=	$this->get_vk_from_json($info_country,'name');
			#страна проживания пользователя
			
			#город пльзователя
			$oauth['city']		=	$this->get_vk_from_json($oauth_info,'city'); //Берём город пользователя
			$info_city		=	json_decode($this->vauth_get_contents($this->api_uri.'places.getCityById?cids='.$oauth['city'].'&access_token='.$oauth['access_token']),FALSE); //Загружаем инфу города
			$oauth['city']		=	$this->get_vk_from_json($info_city,'name'); //Записываем имя города в переменную
			if (empty($oauth['city'])) $oauth['city'] = 'Silent Hill';
			#город пльзователя
			
			#статус пользователя вконтакте
			$oauth['activity']	=	$this->get_vk_from_json($oauth_info,'activity');
			$oauth['skype']	=	$this->get_vk_from_json($oauth_info,'skype');
			$oauth['facebook']	=	$this->get_vk_from_json($oauth_info,'facebook');
			$oauth['facebook_name']	=	$this->get_vk_from_json($oauth_info,'facebook_name');
			$oauth['twitter']	=	$this->get_vk_from_json($oauth_info,'twitter');
			$oauth['livejournal']	=	$this->get_vk_from_json($oauth_info,'livejournal');
			$oauth['university_name']	=	$this->get_vk_from_json($oauth_info,'university_name');
			#статус пользователя вконтакте
			
			$oauth['sex']		=	$this->get_vk_from_json($oauth_info,'sex');

			switch(	$oauth['sex']	) {
			
				case 2	: $oauth['sex']	=	$vauth_text[4];	break;
				case 1	: $oauth['sex']	=	$vauth_text[5];	break;
				case 0	: $oauth['sex']	=	'';	break;
			
			}
			
			$oauth['quotes'] = $this->get_vk_from_json($oauth_info,'quotes');
			$oauth['bio']			=	 $this->get_vk_from_json($oauth_info,'about');
			$oauth['bio']			=		str_replace("\r\n","<br/>",$oauth['bio']);
			$oauth['bio']			=		'<br/>'.$oauth['bio'];
			
			#получаем дату рождения пользователя
			$oauth['bdate']	=	$this->get_vk_from_json($oauth_info,'bdate');//Получаем дату рождения
			
			$oauth['update_time']		=	time();
			$oauth['mobile_phone']		=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['mobile_phone'] ) ) ) );
			$oauth['activity']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['activity'] ) ) ) );
			$oauth['fullname']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['fullname'] ) ) ) );
			$oauth['country']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['country'] ) ) ) );
			$oauth['quotes']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['quotes'] ) ) ) );
			$oauth['bio']			=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['bio'] ) ) ) );
			$oauth['city']				=	$db->safesql( trim( htmlspecialchars( strip_tags( $oauth['city'] ) ) ) );
			
			if (!empty($oauth['country']) and !empty($oauth['city'])) $oauth['land'] = $oauth['country'].', '.$oauth['city'];
			if (empty($oauth['country']) or empty($oauth['city'])) $oauth['land'] = $oauth['country'].$oauth['city'];

			
			return $oauth;
		}

		// ** Функция получения друзей из vkontakte
		function get_oauth_friends($oauth) {

			$site_friends	=	json_decode($this->vauth_get_contents($this->api_uri.'friends.getAppUsers?access_token='.$oauth['access_token']),FALSE); 					
			
			$site_friends	=	$site_friends->response;
			
			foreach($site_friends as $k=>$v) {
				if (is_numeric($v)) {
					$v = sprintf("%.0f",$v);
					$oauth_friendlist	= @$oauth_friendlist.'&'.$v;
				}
			}
			
			$oauth['friends']	= substr($oauth_friendlist,1);
			
			return $oauth['friends'];
			
		}		

		// ** Функция обработки JSON данных пользователя из Вконтакте
		function get_vk_from_json($string,$value) { //Вытягивание информации из ответа в формате json
				if(isset( $string->response[0]->{$value})) {
					return $this->conv_it($string->response[0]->{$value});
				} else return false;
			}	
	}