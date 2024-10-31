<?php
class statisticsNbs extends moduleNbs {
	private $_types = array();
	
	public function init() {
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		parent::init();
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode(). '_newsletter' ] = array(
			'label' => __('Statistics', NBS_LANG_CODE), 'callback' => array($this, 'showForNewsletter'), 'sort_order' => 20, 'child_of' => frameNbs::_()->getModule('newsletters')->getCode(), 'hidden' => 1, 'add_bread' => frameNbs::_()->getModule('newsletters')->getCode(),
		);
		return $tabs;
	}
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'open' => array('id' => 1, 'label' => __('Opened', NBS_LANG_CODE)),
				'uniq_open' => array('id' => 2, 'label' => __('Unique Opened', NBS_LANG_CODE)),
				'click' => array('id' => 3, 'label' => __('Clicked', NBS_LANG_CODE)),
			);
		}
		return $this->_types;
	}
	public function getTypeByCode( $code, $key = false ) {
		$this->getTypes();
		if(isset($this->_types[ $code ])) {
			return $key ? $this->_types[ $code ][ $key ] : $this->_types[ $code ];
		}
		return false;
	}
	public function getUrlForNewsletter( $nid ) {
		$link = frameNbs::_()->getModule('options')->getTabUrl( $this->getCode(). '_newsletter' );
		$link .= '&id='. $nid;
		return $link;
	}
	public function showForNewsletter() {
		$id = (int) reqNbs::getVar('id', 'get');
		return $this->getView()->showForNewsletter( $id );
	}
}