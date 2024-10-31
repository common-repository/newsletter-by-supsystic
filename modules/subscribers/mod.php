<?php
class subscribersNbs extends moduleNbs {
	public function init() {
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		add_action('user_register', array($this, 'syncAddWpSubscriber'));
		add_action('profile_update', array($this, 'syncUpdateWpSubscriber'), 10, 2);
		$this->makeCheckSubscribersPage();
		add_shortcode(NBS_SUBSCRIBERS_PAGE_CONTENT_SHORTCODE, array($this, 'showSubscriberPageContent'));
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
			'label' => __('Subscribers', NBS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-user', 'sort_order' => 50, //'is_main' => true,
		);
		$tabs[ $this->getCode(). '_edit' ] = array(
			'label' => __('Edit', NBS_LANG_CODE), 'callback' => array($this, 'getEditTabContent'), 'sort_order' => 60, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode(). '_add_new' ] = array(
			'label' => __('Create Subscriber', NBS_LANG_CODE), 'callback' => array($this, 'getAddNewTabContent'), 'fa_icon' => 'fa-plus-circle', 'child_of' => $this->getCode(), 'hidden' => 1, 'sort_order' => 70, 'add_bread' => $this->getCode(),
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getAddNewTabContent() {
		return $this->getView()->getEditTabContent();
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
	public function makeCheckSubscribersPage() {
		$pageExists = (int) frameNbs::_()->getModule('options')->get('subs_page_id');
		if(!$pageExists) {
			$pageId = $this->checkSubscribersPage();
			if($pageId && !is_wp_error($pageId)) {
				frameNbs::_()->getModule('options')->save('subs_page_id', $pageId);
			}
		}
	}
	public function getSubscribersPageId() {
		global $wpdb;
		$pageId = (int) frameNbs::_()->getModule('options')->get('subs_page_id');
		if(!$pageId) {
			$pageId = (int) dbNbs::get("SELECT ID FROM $wpdb->posts WHERE post_content LIKE '%[". NBS_SUBSCRIBERS_PAGE_CONTENT_SHORTCODE. "]%' LIMIT 1", 'one');
		}
		return $pageId ? $pageId : false;
	}
	public function getSubscribersPageUrl() {
		$pageId = $this->getSubscribersPageId();
		if($pageId) {
			return get_permalink( $pageId );
		}
		return false;
	}
	public function checkSubscribersPage() {
		$pageId = $this->getSubscribersPageId();
		if(!$pageId) {
			$pageId = wp_insert_post(array(
				'post_title' => __('Subscription Settings', NBS_LANG_CODE),
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_content' => '['. NBS_SUBSCRIBERS_PAGE_CONTENT_SHORTCODE. ']',
			));
		}
		return $pageId;
	}
	public function showSubscriberPageContent() {
		$model = $this->getModel();
		if($model->isLoggedIn()) {
			$sid = $model->isLoggedInId();
			if($sid) {
				$subscriber = $model->getFullById( $sid );
				if($subscriber 
					&& $model->generatePubHash( $subscriber ) == $model->loggedInPubHash()
				) {
					return $this->getView()->showSubscriberPageContent( $subscriber );
				}
			}
		}
		return $this->getView()->showSubscriberLoginPageContent();
	}
}

