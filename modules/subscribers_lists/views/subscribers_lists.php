<?php
class subscribers_listsViewNbs extends viewNbs {
	public function getTabContent() {
		frameNbs::_()->getModule('templates')->loadJqGrid();
		frameNbs::_()->addScript('admin.'. $this->getCode(). '.list', $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.list.js');
		frameNbs::_()->addJSVar('admin.'. $this->getCode(). '.list', 'nbsTblDataUrl', uriNbs::mod($this->getCode(), 'getListForTbl', array('reqType' => 'ajax')));
		frameNbs::_()->addJSVar('admin.'. $this->getCode(). '.list', 'nbsWpListUniqueId', NBS_WP_SUB_LIST);
		
		return parent::getContent('slAdmin');
	}
	public function getEditTabContent($id) {
		$subList = $this->getModel()->getById($id);
		if(empty($subList)) {
			return __('Can not find Subscribe List, sorry...', NBS_LANG_CODE);
		}
		frameNbs::_()->getModule('templates')->loadJqueryUi();
		frameNbs::_()->addScript('admin.'. $this->getCode(). '.edit', $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.edit.js');
		frameNbs::_()->addStyle('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'css/admin.'. $this->getCode(). '.css');
		$this->assign('subList', $subList);
		importClassNbs('csvgeneratorNbs');
		$this->assign('csvGenerator', toeCreateObjNbs('csvgeneratorNbs', array('')));
		dispatcherNbs::addAction('afterAdminBreadcrumbs', array($this, 'showEditSubListFormControls'));
		dispatcherNbs::addAction('adminBreadcrumbsClassAdd', array($this, 'adminBreadcrumbsClassAdd'));
		return parent::getContent('slEditAdmin');
	}
	public function showEditSubListFormControls() {
		parent::display('slEditControls');
	}
	public function adminBreadcrumbsClassAdd() {
		echo ' supsystic-sticky';
	}
}
