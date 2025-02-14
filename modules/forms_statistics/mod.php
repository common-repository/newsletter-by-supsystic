<?php
class forms_statisticsNbs extends moduleNbs {
	private $_types = array();
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'show' => array('id' => 1, 'label' => __('Displayed', NBS_LANG_CODE)),
				'submit' => array('id' => 2, 'label' => __('Submitted', NBS_LANG_CODE)),
				'submit_success' => array('id' => 3, 'label' => __('Subscribe Success', NBS_LANG_CODE)),
				'submit_error' => array('id' => 4, 'label' => __('Subscribe Fail', NBS_LANG_CODE)),
			);
		}
		return $this->_types;
	}
	public function getTypeIdByCode($code) {
		$this->getTypes();
		return isset($this->_types[ $code ]) ? $this->_types[ $code ]['id'] : false;
	}
}