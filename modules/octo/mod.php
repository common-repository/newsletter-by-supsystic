<?php
class octoNbs extends moduleNbs {
	private $_assetsUrl = '';

	public function init() {
		// template_redirect action should be here for normal octo
		//add_action('template_redirect', array($this, 'checkOctoShow'));
		add_action('init', array($this, 'checkEditView'), 99);
		//$this->getModel()->generateInline(4);
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getEditLink($id) {
		return uriNbs::_(array(NBS_CODE. '_tpl_edit' => $id));
	}
	public function getAssetsUrl() {
		if(empty($this->_assetsUrl)) {
			$this->_assetsUrl = frameNbs::_()->getModule('templates')->getCdnUrl(). '_assets/newsletters/';
		}
		return $this->_assetsUrl;
	}
	public function checkEditView() {
		$octoEditId = ((int) reqNbs::getVar(NBS_CODE. '_tpl_edit', 'get'));
		$octoPreview = ((int) reqNbs::getVar('tpl_preview', 'get'));
		$done = false;
		if($octoEditId || $octoPreview) {
			$isAdmin = frameNbs::_()->getModule('user')->isAdmin();
			if($isAdmin) {
				$oid = $octoEditId ? $octoEditId : $octoPreview;
				$this->getView()->renderForPost($oid, array('isEditMode' => $octoEditId));
				$done = true;
			}
		}
		if($done) {
			exit();
		}
	}
	public function getElements() {
		return array(
			'txt' => array('label' => __('Text', NBS_LANG_CODE), 'icon' => 'fa-font'),
			'img' => array('label' => __('Image', NBS_LANG_CODE), 'icon' => 'fa-picture-o'),
			'btn' => array('label' => __('Button', NBS_LANG_CODE), 'icon' => 'fa-hand-pointer-o'),
		);
	}
}

