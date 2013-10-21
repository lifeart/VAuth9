<?php

	class vauthError {
	
	
		private _errors = array();
		private _errorsHtml = '';
		private _errorStringGlue = '<br/>';
	
		function createError() {
		
			if (count($this->_errors)) {
			
				$this->_errorsHtml = implode($this->_errorStringGlue, $array);
				return true;
			
			} else return false;
		
		}
		
		function getError() {
		
			return $this->_errorsHtml;
		
		}
		
		function logError($text) {
		
			$this->_errors[] = $text;
		
		}
		
	
	}