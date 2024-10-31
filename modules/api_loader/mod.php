<?php
class api_loaderNbs extends moduleNbs {
	private $_sources = array();
	private $_wrappers = array();
	
	public function getWrapper( $code ) {
		if(!isset($this->_wrappers[ $code ])) {
			$wrapperClass = $code. '_extApiWrapperNbs';
			importClassNbs('extApiWrapperNbs');
			importClassNbs( $wrapperClass, $this->getModDir(). 'wrappers'. DS. $code. '.php');
			$this->_wrappers[ $code ] = new $wrapperClass( $code );
		}
		return $this->_wrappers[ $code ];
	}
	public function isSupported( $key ) {
		return $this->getWrapper( $key )->isSupported();
	}
	public function getSources() {
		if(empty($this->_sources)) {
			$this->_sources = array(
				'mailchimp' => array('label' => __('MailChimp', NBS_LANG_CODE)),
				'mailpoet' => array('label' => __('MailPoet', NBS_LANG_CODE)),
			);
		}
		return $this->_sources;
	}
	public function getSets() {
		return frameNbs::_()->getModule('options')->get('api_sets');
	}
	public function saveSets( $sets ) {
		frameNbs::_()->getModule('options')->save('api_sets', $sets);
	}
	public function getSet() {
		$keys = func_get_args();
		$sets = $this->getSets();
		$setVal = false;
		foreach($keys as $i => $k) {
			if($i) {
				$setVal = ($setVal && is_array($setVal) && isset($setVal[ $k ])) ? $setVal[ $k ] : false;
			} else {
				$setVal = isset($sets[ $k ]) ? $sets[ $k ] : false;
			}
		}
		return $setVal;
	}
}

