<?php
	
	class VauthCMS {
	
		var $description = 'DataLifeEngine CMS VAuth Class';
		var $name = 'DataLife Engine';
		var $compability = '> 8.2';
		var $version = '0.1';
		var $user = false;
		
		function __cunstruct() {
			include_once('api.class.php');
			$this->api = $dle_api;
			$this->user = new vatuhCmsUser($this->api);
			$this->db = $db;
		}
	
	}
	
	
	class vatuhCmsUser {
		
		var $api = false;
		
		function __construct($api=false) {
			if ($api) $this->api=$api;
		}
		function getByID($id=false) {
			return $this->api->take_user_by_id($id);
		}
		function updateUserLogin($id,$login) {
			return $this->api->change_user_name($id,$login);
		}
		function updateUserEmail($id,$mail) {
			return $this->api->change_user_email($id, $mail)
		}
		function updateUserPassword($id,$password) {
			return $this->change_user_password($id,$password);
		}
		
		function info() {}
		function get() {}
		function set() {}
		function remove() {}
		function login($userinfo) {

			$db = $this->db;
			$dle_api = $this->api;
			$config = $dle_api->dle_config;
			$_TIME = now();
			global $userhash_salt;
			global $userhash_pass;
			
			##	*	Логин пользователя на сайте	
			$_IP = $db->safesql( $_SERVER['REMOTE_ADDR'] );
			$dle_login_hash = "";
			
			function clean_url($url) {
			
				if( $url == '' ) return;	
				$url = str_replace( "http://", "", $url );
				$url = str_replace( "https://", "", $url );
				if( strtolower( substr( $url, 0, 4 ) ) == 'www.' ) $url = substr( $url, 4 );
				$url = explode( '/', $url );
				$url = reset( $url );
				$url = explode( ':', $url );
				$url = reset( $url );					
				return $url;
			
			}

			$domain_cookie = explode (".", clean_url( $_SERVER['HTTP_HOST'] ));
			$domain_cookie_count = count($domain_cookie);
			$domain_allow_count = -2;

			if ( $domain_cookie_count > 2 ) {

				if ( in_array($domain_cookie[$domain_cookie_count-2], array('com', 'net', 'org') )) $domain_allow_count = -3;
				if ( $domain_cookie[$domain_cookie_count-1] == 'ua' ) $domain_allow_count = -3;
				$domain_cookie = array_slice($domain_cookie, $domain_allow_count);
			
			}

			$domain_cookie = "." . implode (".", $domain_cookie);

			define( 'DOMAIN', $domain_cookie );

			function set_cookie($name, $value, $expires) {
				
				if ( $expires ) { $expires = time() + ($expires * 86400); } else { $expires = FALSE; }
				if ( PHP_VERSION < 5.2 ) { setcookie( $name, $value, $expires, "/", DOMAIN . "; HttpOnly" ); } else { setcookie( $name, $value, $expires, "/", DOMAIN, NULL, TRUE ); }
			
			}								

			$password	=	$userinfo['userpassword_hash'];

			if (empty($password)) {

				if (!empty($_SESSION['dle_password'])) {
	
					$password = $_SESSION['dle_password'];
					$password = $this->encode($password);
					$password = base64_encode($password);
					$db->query( "UPDATE " . USERPREFIX . "_users set userpassword_hash='$userpassword_hash' WHERE user_id = '{$userinfo[user_id]}'" );
	
				}

			}
			
			$password = base64_decode( $password );
			$password = $this->encode( $password );

			if ( md5( $password ) != $userinfo['password'] ) die($this->conv_it('Пароли не совпадают)'));
		
			set_cookie( "dle_user_id", $userinfo['user_id'], 365 ); 
			set_cookie( "dle_password", $password, 365 );

			$_SESSION['dle_user_id']		= $userinfo['user_id']; 
			$_SESSION['dle_password']		= $password; 
			$_SESSION['member_lasttime']	= $userinfo['lastdate']; 
			$_SESSION['dle_log'] = 0; 
			
			if (empty($config['key'])) $config['key'] = '';
	
				$dle_login_hash = md5( strtolower( $_SERVER['HTTP_HOST'] . $userinfo['name'] . sha1($password) . $config['key'] . date( "Ymd" ) ) ); 
				
				if ( $config['log_hash'] ) {
				
					if(function_exists('openssl_random_pseudo_bytes')) {
					
						$stronghash = md5(openssl_random_pseudo_bytes(15));
					
					} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
				
					$salt = sha1( str_shuffle("abchefghjkmnpqrstuvwxyz0123456789") . $stronghash );
				
					$_TIME = time();
					$_IP = $_SERVER['REMOTE_ADDR'];
				
					$hash = ''; 
					srand( ( double ) microtime() * 1000000 ); 
					for ($i = 0; $i < 9; $i ++) { $hash .= $salt{rand( 0, 33 )};} 
					$hash = md5( $hash ); 
					$db->query( "UPDATE " . USERPREFIX . "_users set hash='" . $hash . "', lastdate='{$_TIME}', logged_ip='" . $_IP . "' WHERE user_id='$userinfo[user_id]'" ); 
					set_cookie( "dle_hash", $hash, 365 ); 
					$_COOKIE['dle_hash']	= $hash; 
					$dle_userinfo['hash']	= $hash; 
				
				} else $db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users set lastdate='{$_TIME}', logged_ip='" . $_IP . "' WHERE user_id='$userinfo[user_id]'" ); 
			
				$is_logged = TRUE; 	
				return true;
		}
		function register($data) {
		
			global	$dle_api;
			global	$vauth_text;
			global	$vauth_config;

			$reguser = 0;
			
			if (!empty($oauth['email2']) and strlen($oauth['email2'])>3 and !empty($oauth['login2']) and strlen($oauth['login2'])>3) {
				$reguser  = $dle_api->external_register($oauth['login2'],$oauth['password'],$oauth['email2'],$oauth['group']);
				if ($reguser == 1) return $oauth['login2'];
			} elseif (!empty($oauth['login2']) and strlen($oauth['login2'])>3) {
				$reguser  = $dle_api->external_register($oauth['login2'],$oauth['password'],$oauth['email'],$oauth['group']);
				if ($reguser == 1) return $oauth['login2'];
			} elseif (!empty($oauth['email2']) and strlen($oauth['email2'])>3) {
				$oauth['email'] = $oauth['email2'];
			}
			
			if ($reguser != 1) {
			
				if	($reguser == -4) {die($this->conv_it($vauth_text['bad_group']));}
				
				function if_is($data,$datalink) {if (!empty($data[$datalink])) return $data[$datalink];else return '';}

				if ($this->function_enabled('mb_substr')) {
					$f_fname = mb_substr( strtolower(if_is($oauth,'firstname')), 0, 1 );
					$f_lname = mb_substr( strtolower(if_is($oauth,'lastname')), 0, 1 );
				} else {
					$f_fname = substr( strtolower(if_is($oauth,'firstname')), 0, 1 );
					$f_lname = substr( strtolower(if_is($oauth,'lastname')), 0, 1 );		
				}
				
				$name = array();
				
				if ( isset($oauth['nick']) and $oauth['nick'] == 'id'.$oauth['uid'] ) $oauth['nick'] = '';
				if ( isset($oauth['nick']) and $oauth['nick'] ==  $oauth['uid'] ) $oauth['nick'] = '';
		
				if ( strpos($oauth['lastname'], " ") > 0 ) {
					$oauth['lastname'] = substr($oauth['lastname'], 0, strpos($oauth['lastname'], " "));
					}
				
				if ( strpos($oauth['firstname'], " ") > 0 ) {
					$oauth['firstname'] = substr($oauth['firstname'], (strpos($oauth['lastname'], " ")+1),strlen($oauth['lastname']));
					}
				
				$name[1] = if_is($oauth,'nick');
				$name[2] = $f_fname.'.'.strtolower($oauth['lastname']);
				$name[3] = $f_lname.'.'.strtolower($oauth['firstname']);
				$name[4] = if_is($oauth,'fullname');
				$name[5] = if_is($oauth,'lastname').' '.if_is($oauth,'firstname');
				$name[6] = $oauth['nick'].mt_rand(75,500);
				$name[7] = $oauth['firstname'].mt_rand(75,500);
				$name[8] = $oauth['lastname'].mt_rand(75,500);
				$name[9] = $oauth['fullname'].' '.mt_rand(75,500);
				$name[10] = 'User_'.mt_rand(1,500);
				$name[13] = 'Anonymous_'.mt_rand(75,500);
				$name[12] = 'Neo_'.mt_rand(75,500);
				$name[11] = 'Pipito_'.mt_rand(75,500);
				$name[14] = $oauth['uid'];
				ksort($name);
				
				if (empty($oauth['email'])) $oauth['email'] = $oauth['uid'].$vauth_text['def_email'];

				for ($n=1;$n<=14;$n++) {
					if (strlen($name[$n])>3) {
						$reguser = $dle_api->external_register($name[$n],$oauth['password'],$oauth['email'],$oauth['group']);
						if ( $reguser == 1 ) {return $name[$n];break;}
						if ( $reguser == -2 ) break;
						if ( $reguser == -3 ) $oauth['email'] = $oauth['uid'].$vauth_text['def_email'];
						if ( $reguser == -4)  $oauth['group'] = 4;	
					}
				}
				
				if	($reguser == -2) {
					// Авторизация пользователя по e-mail ))
					if ($vauth_config['email_auth'] == 1) {
						$userinfo = $dle_api->take_user_by_email($oauth['email']);
						if (!empty($userinfo['userpassword_hash'])) $this->user_login($userinfo);
						else die($this->conv_it($oauth['email'].$vauth_text['email_error']));
					} else die($this->conv_it($oauth['email'].$vauth_text['email_error']));	
				}
				die($this->conv_it($vauth_text['fatal_reg_error']));
			} else return $oauth['login2'];		
		
		}
		
		function check();
	
	}