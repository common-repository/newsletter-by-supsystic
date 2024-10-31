<?php
class importerNbs extends moduleNbs {
	public function init() {
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function syncAddWpSubscriber( $wpUserId ) {
		$wpUser = get_user_by('ID', $wpUserId);
		if($wpUser && !is_wp_error($wpUser) && user_can($wpUser, 'subscriber')) {
			$this->getModel()->duplicateFromWpUser( $wpUser );
		}
	}
	public function syncUpdateWpSubscriber( $wpUserId, $oldUserData ) {
		$wpUser = get_user_by('ID', $wpUserId);
		if($wpUser && !is_wp_error($wpUser) && user_can($wpUser, 'subscriber')) {
			$this->getModel()->duplicateFromWpUser( $wpUser );
		}
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Importer', NBS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-users', 'sort_order' => 65, 'hidden' => 1, 'add_bread' => $this->getCode()
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
}

