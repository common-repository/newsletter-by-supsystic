<?php
class newslettersViewNbs extends viewNbs {
	public function getTabContent() {
		frameNbs::_()->getModule('templates')->loadJqGrid();
		frameNbs::_()->addStyle('admin.newsletters', $this->getModule()->getModPath(). 'css/admin.newsletters.css');
		frameNbs::_()->addScript('admin.newsletters', $this->getModule()->getModPath(). 'js/admin.newsletters.js');
		frameNbs::_()->addScript('admin.newsletters.list', $this->getModule()->getModPath(). 'js/admin.newsletters.list.js');
		frameNbs::_()->addJSVar('admin.newsletters.list', 'nbsTblDataUrl', uriNbs::mod('newsletters', 'getListForTbl', array('reqType' => 'ajax')));

		$this->assign('addNewLink', frameNbs::_()->getModule('options')->getTabUrl('newsletters_add_new'));
		return parent::getContent('nlAdmin');
	}
	public function getAddNewTabContent() {
		frameNbs::_()->getModule('templates')->loadJqueryUi();
		frameNbs::_()->addStyle('admin.newsletters', $this->getModule()->getModPath(). 'css/admin.newsletters.css');
		frameNbs::_()->addScript('admin.newsletters', $this->getModule()->getModPath(). 'js/admin.newsletters.js');
		frameNbs::_()->getModule('templates')->loadMagicAnims();

		$changeFor = (int) reqNbs::getVar('change_for', 'get');
		//frameNbs::_()->addJSVar('admin.newsletters', 'nbsChangeFor', array($changeFor));
		if($changeFor) {
			$originalNewsletter = $this->getModel()->getById( $changeFor );
			$editLink = $this->getModule()->getEditLink( $changeFor );
			$this->assign('originalNewsletter', $originalNewsletter);
			$this->assign('editLink', $editLink);
			frameNbs::_()->addJSVar('admin.newsletters', 'nbsOriginalNewsletter', $originalNewsletter);
			dispatcherNbs::addFilter('mainBreadcrumbs', array($this, 'modifyBreadcrumbsForChangeTpl'));
		}

		$this->assign('list', dispatcherNbs::applyFilters('showTplsList', frameNbs::_()->getModule('octo')->getModel()
			->getPresetTemplates()));
		$usersTplList = frameNbs::_()->getModule('octo')->getModel()->getPresetTemplates( false );
		if(!empty($usersTplList)) {	// Collect newsletters names, where those templates was used - to make template list more clear
			$oids = array();
			foreach($usersTplList as $o) {
				$oids[ $o['id'] ] = 1;
			}
			$usedInNewsletters = $this->getModel()->getSimpleList('oid IN ('. implode(',', array_keys($oids)). ')');
			if(!empty($usedInNewsletters)) {
				foreach($usersTplList as $i => $o) {
					foreach($usedInNewsletters as $n) {
						if($n['oid'] == $o['id']) {
							$usersTplList[ $i ]['newsletter_label'] = $n['label'];
							break;
						}
					}
				}
			}
			// For case when we will have 2 sections - original and user edited - tempalates - we will need tabs
			frameNbs::_()->addScript('wp.tabs', NBS_JS_PATH. 'wp.tabs.js');
		}
		$this->assign('usersTplList', $usersTplList);
		$this->assign('changeFor', $changeFor);
		$this->assign('listsForSelect', frameNbs::_()->getModule('subscribers_lists')->getListsForSelect(array('subs_cnt' => true)));
		return parent::getContent('nlAddNewAdmin');
	}
	public function modifyBreadcrumbsForChangeTpl($crumbs) {
		$crumbs[ count($crumbs) - 1 ]['label'] = __('Modify Newsletter Template', NBS_LANG_CODE);
		return $crumbs;
	}
	public function adminBreadcrumbsClassAdd() {
		echo ' supsystic-sticky';
	}
	public function getEditTabContent( $id ) {
		$newsletter = $this->getModel()->getFullById($id);
		if(empty($newsletter)) {
			return __('Cannot find required Newsletter', NBS_LANG_CODE);
		}
		// Check if it is sending right now, And if so - pause it
		if($newsletter['status'] == $this->getModel()->getStatusByCode('sending', 'id')) {
			$this->getModel()->setPause( $newsletter['id'] );
		}
		dispatcherNbs::doAction('beforeNewsletterEdit', $newsletter);

		dispatcherNbs::addAction('afterAdminBreadcrumbs', array($this, 'showEditNewsletterFormControls'));
		dispatcherNbs::addAction('adminBreadcrumbsClassAdd', array($this, 'adminBreadcrumbsClassAdd'));
		if(empty($newsletter['ab_id'])) {
			dispatcherNbs::addFilter('mainBreadcrumbs', array($this, 'changeMainBreadCrumbsClb'));
		}
		if(!is_array($newsletter['params']))
			$newsletter['params'] = array();

		frameNbs::_()->getModule('templates')->loadJqueryUi();
		frameNbs::_()->getModule('templates')->loadBootstrapPartialOnlyCss();

		$nbsAddNewUrl = frameNbs::_()->getModule('options')->getTabUrl('newsletters_add_new');
		frameNbs::_()->addStyle('admin.newsletters', $this->getModule()->getModPath(). 'css/admin.newsletters.css');

		frameNbs::_()->addScript('admin.newsletters', $this->getModule()->getModPath(). 'js/admin.newsletters.js');
		frameNbs::_()->addScript('admin.newsletters.edit', $this->getModule()->getModPath(). 'js/admin.newsletters.edit.js');
		frameNbs::_()->addJSVar('admin.newsletters.edit', 'nbsNewsletter', $newsletter);
		frameNbs::_()->addJSVar('admin.newsletters.edit', 'nbsAddNewUrl', $nbsAddNewUrl);

		frameNbs::_()->addScript('wp.tabs', NBS_JS_PATH. 'wp.tabs.js');

		$this->assign('adminEmail', get_bloginfo('admin_email'));
		$this->assign('siteName', wp_specialchars_decode(get_bloginfo('name')));
		$this->assign('isPro', frameNbs::_()->getModule('supsystic_promo')->isPro());
		$this->assign('mainLink', frameNbs::_()->getModule('supsystic_promo')->getMainLink());
		$this->assign('promoModPath', frameNbs::_()->getModule('supsystic_promo')->getAssetsUrl());

		$this->assign('nbsAddNewUrl', $nbsAddNewUrl);
		$this->assign('listsForSelect', frameNbs::_()->getModule('subscribers_lists')->getListsForSelect(array('subs_cnt' => true)));

		$this->assign('editOctoUrl', frameNbs::_()->getModule('octo')->getEditLink( $newsletter['oid'] ));
		$this->assign('previewUrl', uriNbs::mod('newsletters', 'getPreviewHtml', array('id' => $id)));
		$this->assign('newsletter', $newsletter);
		wp_enqueue_script('jQuery');
		// Time selects
		$this->assign('timeRange', utilsNbs::getTimeRange());
		// Week days selects
		$this->assign('weekDaysRange', utilsNbs::getWeekDaysArray());
		// Month days selects
		$this->assign('monthDaysRange', utilsNbs::getMonthDaysArray());

		$tabs = array(
			'nbsNewsletterMain' => array(
				'title' => __('Main', NBS_LANG_CODE),
				'content' => $this->getMainNewsletterTab(),
				'fa_icon' => 'fa-cog',
				'sort_order' => 0),
			'nbsSendOptions' => array(
				'title' => __('Send Options', NBS_LANG_CODE),
				'content' => $this->getSendOptsNewsletterTab(),
				'fa_icon' => 'fa-envelope-o',
				'sort_order' => 20),
			'nbsNewsletterSubscribers' => array(
				'title' => __('Recipients', NBS_LANG_CODE),
				'content' => $this->getSubscribersNewsletterTab(),
				'fa_icon' => 'fa-users',
				'sort_order' => 30),
		);
		//nlSendOptions
		$tabs = dispatcherNbs::applyFilters('newslettersEditTabs', $tabs, $newsletter);
		uasort($tabs, array($this, 'sortEditNewsletterTabsClb'));
		$this->assign('tabs', $tabs);
		dispatcherNbs::doAction('beforeNewsletterEditRender', $newsletter);
		return parent::getContent('nlEditAdmin');
	}
	public function getSendOptsNewsletterTab() {
		return parent::getContent('nlSendOptions');
	}
	public function changeMainBreadCrumbsClb($crumbs) {
		return array( $crumbs[ count($crumbs) - 1 ] );	// Get rid of all other breadcrumbs - leave space on this page for other important things (buttons, etc.)
	}
	public function showEditNewsletterFormControls() {
		parent::display('nlEditControls');
	}
	public function sortEditNewsletterTabsClb($a, $b) {
		if($a['sort_order'] > $b['sort_order'])
			return 1;
		if($a['sort_order'] < $b['sort_order'])
			return -1;
		return 0;
	}
	public function getMainNewsletterTab() {
		return parent::getContent('nlEditMain');
	}
	public function getSubscribersNewsletterTab() {
		return parent::getContent('nlEditSubscribers');
	}
	public function getMainNewsletterCodeTab() {
		return parent::getContent('newslettersEditAdminCodeOpts');
	}
}
