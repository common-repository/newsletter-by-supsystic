<?php
class subscribers_listsNbs extends moduleNbs {
	public function init() {
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		$this->makeCheckWpSubscribersList();
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Subscription Lists', NBS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-list-alt', 'sort_order' => 30, //'is_main' => true,
		);
		$tabs[ $this->getCode(). '_edit' ] = array(
			'label' => __('Edit', NBS_LANG_CODE), 'callback' => array($this, 'getEditTabContent'), 'sort_order' => 40, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		return $tabs;
	}
	public function getTabContent() {
		// Make this force check one more time when we are on lists page
		$this->checkWpSubscribersList();
		return $this->getView()->getTabContent();
	}
	public function getEditTabContent() {
		$id = (int) reqNbs::getVar('id', 'get');
		return $this->getView()->getEditTabContent( $id );
	}
	public function getEditLink($id) {
		$link = frameNbs::_()->getModule('options')->getTabUrl( $this->getCode(). '_edit' );
		$link .= '&id='. $id;
		return $link;
	}
	public function getListsForSelect( $params = array() ) {
		$withSubsCnt = isset($params['subs_cnt']) ? $params['subs_cnt'] : false;
		$listsForSelect = array();
		$lists = $this->getModel()->getSimpleList();
		if(!empty($lists)) {
			foreach($lists as $l) {
				$label = $l['label'];
				if($withSubsCnt) {
					$label .= ' ('. $l['subscribers_cnt']. ')';
				}
				$listsForSelect[ $l['id'] ] = $label;
			}
		}
		return $listsForSelect;
	}
	public function makeCheckWpSubscribersList() {
		$wpListInported = (int) frameNbs::_()->getModule('options')->get('wp_list_imported');
		if(!$wpListInported) {
			$this->checkWpSubscribersList();
			frameNbs::_()->getModule('options')->save('wp_list_imported', 1);
		}
	}
	public function checkWpSubscribersList() {
		$model = $this->getModel();
		$wpListId = $model->getWpListId();
		if(empty($wpListId)) {	// WP List should be in any case, if it's not ther - create it
			$id = $model->createWpList();
			if($id) {
				$model->importToWpList( $id );
			}
		}
	}
	public function generateListsDesc( $lists ) {
		$listsDesc = array();
		foreach($lists as $l) {
			$listsDesc[] = '<a href="'. $this->getEditLink( $l['id'] ). '" target="_blank">'. $l['label']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';
		}
		return implode(', ', $listsDesc);
	}
}

