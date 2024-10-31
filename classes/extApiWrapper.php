<?php
abstract class extApiWrapperNbs extends baseObjectNbs {
	private $_code = '';
	private $_notSupportErrorPushed = false;
	
	public function __construct( $code ) {
		$this->_code = $code;
	}

	public function getLists() {}
	public function import() {}
	
	protected function _afterSuccessImport( $slid, $insertCnt ) {
		
	}
	
	protected function _includLibFile( $file ) {
		require_once( $this->getModule()->getModDir(). 'lib'. DS. $this->_code. DS. $file);
	}
	public function isSupported() {
		return true;
	}
	public function getSet() {
		$args = func_get_args();
		$getSetArgs = array_merge(array( $this->_code ), $args);
		return call_user_func_array(array($this->getModule(), 'getSet'), $getSetArgs);
	}
	public function getModule() {
		return frameNbs::_()->getModule('api_loader');
	}
	public function getCode() {
		return $this->_code;
	}
	public function getOpts() {
		return array();
	}
	public function pushNotSupportError( $error, $key = '' ) {
		if(!$this->_notSupportErrorPushed) {
			$this->pushError($error, $key);
			$this->_notSupportErrorPushed = true;
		}
	}
}