<?php
class importerViewNbs extends viewNbs {
	public function getTabContent() {
		frameNbs::_()->getModule('templates')->loadJqGrid();
		$this->getModule()->addAdminScript();
		$this->getModule()->addAdminJSVar('nbsApiSets', frameNbs::_()->getModule('api_loader')->getSets());
		$this->getModule()->addAdminStyle();
		
		
		$importSourceForSelect = array();
		$importSources = frameNbs::_()->getModule('api_loader')->getSources();
		foreach($importSources as $sCode => $sData) {
			$wrapper = frameNbs::_()->getModule('api_loader')->getWrapper( $sCode );
			if($wrapper->isSupported())
				$importSourceForSelect[ $sCode ] = $sData['label'];
		}
		$this->assign('importSourceForSelect', $importSourceForSelect);
		$this->assign('listsForSelect', frameNbs::_()->getModule('subscribers_lists')->getListsForSelect(array('subs_cnt' => true)));
		return parent::getContent('impAdmin');
	}
	/**
	 * Just a wrapper for module method getSet() - to be able to use it in template
	 * @return type
	 */
	public function getSet() {
		$keys = func_get_args();
		return call_user_func_array(array(frameNbs::_()->getModule('api_loader'), 'getSet'), $keys);
	}
}
