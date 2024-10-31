<?php
class subscribersViewNbs extends viewNbs {
	private $_currentId = 0;
	
	public function getTabContent() {
		frameNbs::_()->getModule('templates')->loadJqGrid();
		$this->getModule()->addAdminScript('list');
		$this->getModule()->addAdminJSVar('nbsTblDataUrl', uriNbs::mod('subscribers', 'getListForTbl', array('reqType' => 'ajax')), 'list');
		
		$subscribersListsForSelectLists = frameNbs::_()->getModule('subscribers_lists')->getListsForSelect();
		$listsForSelect = array(0 => __('All', NBS_LANG_CODE));
		foreach($subscribersListsForSelectLists as $slid => $slLabel) {
			$listsForSelect[ $slid ] = $slLabel;
		}
		$this->assign('listsForSelect', $listsForSelect);
		$this->assign('addNewLink', frameNbs::_()->getModule('options')->getTabUrl( $this->getCode(). '_add_new' ));
		$this->assign('importLink', frameNbs::_()->getModule('options')->getTabUrl( 'importer' ));
		return parent::getContent('sAdmin');
	}
	public function getEditTabContent($id = 0) {
		frameNbs::_()->getModule('templates')->loadJqueryUi();
		
		$subscriber = false;
		if($id) {
			$this->_currentId = $id;
			$subscriber = $this->getModel()->getFullById( $id );
		}
		dispatcherNbs::addAction('afterAdminBreadcrumbs', array($this, 'showEditSubFormControls'));
		dispatcherNbs::addAction('adminBreadcrumbsClassAdd', array($this, 'adminBreadcrumbsClassAdd'));
		$this->getModule()->addAdminScript('edit');
		if($subscriber) {
			$this->getModule()->addAdminJSVar('nbsSubscriber', $subscriber, 'edit');
		}
		$nbsAddNewUrl = frameNbs::_()->getModule('options')->getTabUrl('subscribers_add_new');
		$this->getModule()->addAdminJSVar('nbsAddNewUrl', $nbsAddNewUrl, 'edit');

		$statusList = $this->getModel()->getStatuses();
		$statusListForSelect = array();
		foreach($statusList as $s) {
			$statusListForSelect[ $s['id'] ] = $s['label'];
		}
		if($subscriber && $subscriber['form_id']) {	// Subscribed from form
			$subscribedForm = frameNbs::_()->getModule('forms')->getModel()->getById( $subscriber['form_id'] );
			if(!empty($subscribedForm)) {
				$this->assign('subscribedForm', $subscribedForm);
			}
		}
		if($subscriber 
			&& $subscriber['all_data'] 
			&& isset($subscriber['all_data']['popup_id']) 
			&& !empty($subscriber['all_data']['popup_id'])
			&& class_exists('framePps')
		) {
			$this->assign('subscribedPopUp', framePps::_()->getModule('popup')->getModel()->getById($subscriber['all_data']['popup_id']));
		}
		$this->assign('listsForSelect', frameNbs::_()->getModule('subscribers_lists')->getListsForSelect());
		$this->assign('statusListForSelect', $statusListForSelect);
		$this->assign('subscriber', $subscriber);
		return parent::getContent('sEditAdmin');
	}
	public function showEditSubFormControls() {
		$this->assign('currentId', $this->_currentId);
		parent::display('sEditControls');
	}
	public function adminBreadcrumbsClassAdd() {
		echo ' supsystic-sticky';
	}
	public function showSubscriberPageContent( $subscriber ) {
		frameNbs::_()->getModule('templates')->loadCoreJs();
		frameNbs::_()->getModule('templates')->loadCoreCss();
		frameNbs::_()->addScript('frontend.subscribers.profile', $this->getModule()->getModPath(). 'js/frontend.subscribers.profile.js');
		
		$subListsIds = $subLists = array();
		if(!empty($subscriber['slid'])) {
			$subListsIds = array_merge($subListsIds, $subscriber['slid']);
		}
		$prevSubscribed = $this->getModel()->getPrevListsIds( $subscriber['id'] );
		if(!empty($prevSubscribed)) {
			$subListsIds = array_merge($subListsIds, $prevSubscribed);
		}
		// $subListsIds simply can't be empty: somhow this subscriber subscribed to something, right?
		if(!empty($subListsIds)) {
			$subLists = frameNbs::_()->getModule('subscribers_lists')
				->getModel()
				->setWhere("id IN (". implode(',', $subListsIds).")")
				->getFromTbl();
		}
		$this->assign('subLists', $subLists);
		$this->assign('standardFieldLabels', array(
			'first_name' => __('First Name', NBS_LANG_CODE),
			'last_name' => __('Last Name', NBS_LANG_CODE),
		));
		$this->assign('subscriber', $subscriber);
		return parent::getContent('sPageContent');
	}
	public function showSubscriberLoginPageContent() {
		frameNbs::_()->getModule('templates')->loadCoreJs();
		frameNbs::_()->getModule('templates')->loadCoreCss();
		frameNbs::_()->addScript('frontend.subscribers.login', $this->getModule()->getModPath(). 'js/frontend.subscribers.login.js');
		return parent::getContent('sPageLoginContent');
	}
}
