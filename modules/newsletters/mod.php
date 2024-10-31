<?php
class newslettersNbs extends moduleNbs {
	private $_assetsUrl = '';

	public function init() {
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		// Add to admin bar new item
		add_action('admin_bar_menu', array($this, 'addAdminBarNewItem'), 300);
		///add_action('wp_insert_post', array($this, 'checkNewContentNewsletters'), 10, 2);
		add_action('transition_post_status', array($this, 'checkNewContentNewsletters'), 10, 3);
		$this->checkClick();
		parent::init();
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode(). '_add_new' ] = array(
			'label' => __('Create Newsletter', NBS_LANG_CODE), 'callback' => array($this, 'getAddNewTabContent'), 'fa_icon' => 'fa-plus-circle', 'sort_order' => 10, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode(). '_edit' ] = array(
			'label' => __('Edit', NBS_LANG_CODE), 'callback' => array($this, 'getEditTabContent'), 'sort_order' => 20, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode() ] = array(
			'label' => __('Newsletters', NBS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-envelope', 'sort_order' => 20, //'is_main' => true,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getAddNewTabContent() {
		return $this->getView()->getAddNewTabContent();
	}
	public function getEditTabContent() {
		$id = (int) reqNbs::getVar('id', 'get');
		return $this->getView()->getEditTabContent( $id );
	}
	public function getEditLink($id, $newslettersTab = '') {
		$link = frameNbs::_()->getModule('options')->getTabUrl( $this->getCode(). '_edit' );
		$link .= '&id='. $id;
		if(!empty($newslettersTab)) {
			$link .= '#'. $newslettersTab;
		}
		return $link;
	}
	public function getAssetsUrl() {
		if(empty($this->_assetsUrl)) {
			$this->_assetsUrl = frameNbs::_()->getModule('templates')->getCdnUrl(). '_assets/newsletters/';
		}
		return $this->_assetsUrl;
	}
	public function addAdminBarNewItem( $wp_admin_bar ) {
		$mainCap = frameNbs::_()->getModule('adminmenu')->getMainCap();
		if(!current_user_can( $mainCap) || !$wp_admin_bar || !is_object($wp_admin_bar)) {
			return;
		}
		$wp_admin_bar->add_menu(array(
			'parent'    => 'new-content',
			'id'        => NBS_CODE. '-admin-bar-new-item',
			'title'     => __('Newsletter', NBS_LANG_CODE),
			'href'      => frameNbs::_()->getModule('options')->getTabUrl( $this->getCode(). '_add_new' ),
		));
	}
	public function checkNewContentNewsletters($newStatus, $oldStatus, $post) {
		if ('publish' !== $newStatus || 'publish' === $oldStatus)
			return;
		$ID = $post->ID;
		$model = $this->getModel();
		$newContentNewsletters = $model
			->setWhere("status IN (". implode(',', array($model->getStatusByCode('sending', 'id'), $model->getStatusByCode('waiting', 'id'))).") "
				. "AND send_on = ". $model->getSendOnByCode('new_content', 'id'))
			->getFromTbl();
		if(!empty($newContentNewsletters)) {
			frameNbs::_()->getModule('log')->addLine("newsletters::add for new content - post $ID - adding ". count($newContentNewsletters). " letters");
			$queueModel = frameNbs::_()->getModule('queue')->getModel();
			$currentDbDate = $currentDbTs = $dbDateTime = NULL;
			foreach($newContentNewsletters as $n) {
				$time = NULL;
				switch($n['params']['main']['send_on_new_content']) {
					case 'monthly':
						if(!$currentDbDate) {
							$currentDbDate = dbNbs::getCurrentDate();
							$currentDbTs = strtotime($currentDbDate);
							$dbDateTime = explode(' ', $currentDbDate);
						}
						$dbYearMonthDay = explode('-', $dbDateTime[ 0 ]);
						$sendDay = (int) $n['params']['main']['send_on_new_content_monthly_day'];
						if((int)$dbYearMonthDay[ 2 ] >= $sendDay) {	// Day in this month - passed, leap to next month
							$dbYearMonthDay[ 1 ] = (int) $dbYearMonthDay[ 1 ] + 1;
							if($dbYearMonthDay[ 1 ] > 12) {	// If current month was last - eap to next one was bad idea
								$dbYearMonthDay[ 0 ] = (int) $dbYearMonthDay[ 0 ] + 1;	// Leap to next year
								$dbYearMonthDay[ 1 ] = 1;	// To first month of next year
							}
						}
						$dbYearMonthDay[ 2 ] = $sendDay;
						$time = implode('-', $dbYearMonthDay). ' '. date("H:i", strtotime($n['params']['main']['send_on_new_content_monthly_time'])). ':00';
						break;
					case 'weekly':
						$time = date(NBS_DB_DATE_FORMAT, strtotime('next '. $n['params']['main']['send_on_new_content_weekly_day']));
						$timeArr = explode(' ', $time);
						$time = $timeArr[ 0 ]. ' '. date("H:i", strtotime($n['params']['main']['send_on_new_content_weekly_time'])). ':00';
						break;
					case 'daily':
						if(!$currentDbDate) {
							$currentDbDate = dbNbs::getCurrentDate();
							$currentDbTs = strtotime($currentDbDate);
							$dbDateTime = explode(' ', $currentDbDate);
						}
						$sendTime = date("H:i", strtotime($n['params']['main']['send_on_new_content_daily_time']));
						$time = $dbDateTime[ 0 ]. ' '. $sendTime. ':00';
						if(strtotime($time) < $currentDbTs) {	// In thisday this time was passed - set it to next day
							$time = date(NBS_DB_DATE_FORMAT, strtotime('+1 day', strtotime($time)));
						}
						break;
					case 'immediately': default: 
						break;
				}
				$queueModel->addNewsletter( $n['id'], $time );
			}
		}
	}
	public function checkClick() {
		$nid = (int) reqNbs::getVar(NBS_CODE. '_nid', 'get');
		if($nid) {
			$sid = (int) reqNbs::getVar(NBS_CODE. '_sid', 'get');
			if($sid) {
				$trackClick = (int) reqNbs::getVar(NBS_CODE. '_track_click', 'get');
				if($trackClick) {
					frameNbs::_()->getModule('statistics')->getModel()->add(array(
						'nid' => $nid,
						'sid' => $sid,
						'type' => 'click',
					));
					$redirect = reqNbs::getVar(NBS_CODE. '_ext_go', 'get');
					if(!empty($redirect)) {
						redirectNbs( $redirect );
					}
				}
			}
		}
	}
}

