<?php

	class vauthCmsModel {
	
		var $className = 'vauthCmsModel';
		var $cms_path = '../cms';
		var $cms_file_prefix = '';
		var $cms_file_postfix = '.controller.php';
		// var $errors = new vauthError();
		
		function __construct($cms=false) {
			if (!$cms) $this->errors->logError('Невозможно создать модель, так как не указана CMS');
			$this->_loadCmsData($cms);
		}
		
		private function _loadCmsData($cms=false) {
			$cms = trim(mb_strtolower($cms));
			if (!empty($cms) && ctype_alpha($cms) == true) {
				$fname = $this->cms_path . '/' . $this->cms_file_prefix . $cms . $this->cms_file_postfix;
				if (file_exists($fname)) {
					require_once($fname);
					$this->cms = new VauthCMS();
				} else newError('no cms controller file',1);
			}  else newError('empty cms name',1);
		}
	}