<?php
class formsNbs extends moduleNbs {
	private $_assetsUrl = '';
	private $_formsPrevUrl = '';
	private $_fieldTypes = array();

	public function init() {
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		add_shortcode(NBS_FORM_SHORTCODE, array($this, 'showForm'));
		// Add to admin bar new item
		add_action('admin_bar_menu', array($this, 'addAdminBarNewItem'), 300);
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode(). '_add_new' ] = array(
			'label' => __('Add New Form', NBS_LANG_CODE), 'callback' => array($this, 'getAddNewTabContent'), 'fa_icon' => 'fa-plus-circle', 'hidden' => 1, 'sort_order' => 10, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode(). '_edit' ] = array(
			'label' => __('Edit', NBS_LANG_CODE), 'callback' => array($this, 'getEditTabContent'), 'sort_order' => 25, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode() ] = array(
			'label' => __('Subscribe Forms', NBS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-list', 'sort_order' => 25, //'is_main' => true,
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
	public function getEditLink($id, $formsTab = '') {
		$link = frameNbs::_()->getModule('options')->getTabUrl( $this->getCode(). '_edit' );
		$link .= '&id='. $id;
		if(!empty($formsTab)) {
			$link .= '#'. $formsTab;
		}
		return $link;
	}
	public function getAssetsUrl() {
		if(empty($this->_assetsUrl)) {
			$this->_assetsUrl = frameNbs::_()->getModule('templates')->getCdnUrl(). '_assets/forms/';
		}
		return $this->_assetsUrl;
	}
	public function getFormPrevUrl() {
		if(empty($this->_formsPrevUrl)) {
			$this->_formsPrevUrl = frameNbs::_()->getModule('newsletters')->getAssetsUrl(). 'forms/';
		}
		return $this->_formsPrevUrl;
	}
	public function addAdminBarNewItem( $wp_admin_bar ) {
		$mainCap = frameNbs::_()->getModule('adminmenu')->getMainCap();
		if(!current_user_can( $mainCap) || !$wp_admin_bar || !is_object($wp_admin_bar)) {
			return;
		}
		$wp_admin_bar->add_menu(array(
			'parent'    => 'new-content',
			'id'        => NBS_CODE. '-admin-bar-new-item',
			'title'     => __('Form', NBS_LANG_CODE),
			'href'      => frameNbs::_()->getModule('options')->getTabUrl( $this->getCode(). '_add_new' ),
		));
	}
	public function getFieldTypes() {
		if(empty($this->_fieldTypes)) {
			$this->_fieldTypes = dispatcherNbs::applyFilters('fieldTypes', array(
				'text' => array('label' => __('Text', NBS_LANG_CODE), 'icon' => 'fa-font'),
				'email' => array('label' => __('Email', NBS_LANG_CODE), 'icon' => 'fa-envelope-o'),
				'selectbox' => array('label' => __('Select Box', NBS_LANG_CODE), 'icon' => 'fa-list-ul'),
				'selectlist' => array('label' => __('Select List', NBS_LANG_CODE), 'icon' => 'fa-th-list'),
				'textarea' => array('label' => __('Textarea', NBS_LANG_CODE), 'icon' => 'fa-font'),
				'radiobutton' => array('label' => __('Radiobutton', NBS_LANG_CODE), 'icon' => 'fa-dot-circle-o'),
				'radiobuttons' => array('label' => __('Radiobuttons List', NBS_LANG_CODE), 'icon' => 'fa-dot-circle-o'),
				'checkbox' => array('label' => __('Checkbox', NBS_LANG_CODE), 'icon' => 'fa-check-square-o'),
				'checkboxlist' => array('label' => __('Checkbox List', NBS_LANG_CODE), 'icon' => 'fa-check-square-o'),
				'countryList' => array('label' => __('Country List', NBS_LANG_CODE), 'icon' => 'fa-globe'),
				'countryListMultiple' => array('label' => __('Country List Multiple', NBS_LANG_CODE), 'icon' => 'fa-globe'),

				'number' => array('label' => __('Number', NBS_LANG_CODE), 'icon' => 'fa-sort-numeric-asc'),

				'date' => array('label' => __('Date', NBS_LANG_CODE), 'icon' => 'fa-calendar'),
				'month' => array('label' => __('Month', NBS_LANG_CODE), 'icon' => 'fa-calendar'),
				'week' => array('label' => __('Week', NBS_LANG_CODE), 'icon' => 'fa-calendar'),
				'time' => array('label' => __('Time', NBS_LANG_CODE), 'icon' => 'fa-clock-o'),

				'color' => array('label' => __('Color', NBS_LANG_CODE), 'icon' => 'fa-paint-brush'),
				'range' => array('label' => __('Range', NBS_LANG_CODE), 'icon' => 'fa-magic'),
				'url' => array('label' => __('URL', NBS_LANG_CODE), 'icon' => 'fa-link'),

				//'file' => array('label' => __('File Upload', NBS_LANG_CODE), 'icon' => 'fa-upload', 'pro' => ''),
				'recaptcha' => array('label' => __('reCaptcha', NBS_LANG_CODE), 'icon' => 'fa-unlock-alt'),

				'hidden' => array('label' => __('Hidden Field', NBS_LANG_CODE), 'icon' => 'fa-eye-slash'),
				'submit' => array('label' => __('Submit Button', NBS_LANG_CODE), 'icon' => 'fa-paper-plane-o'),
				'reset' => array('label' => __('Reset Button', NBS_LANG_CODE), 'icon' => 'fa-repeat'),

				'htmldelim' => array('label' => __('HTML / Text Delimiter', NBS_LANG_CODE), 'icon' => 'fa-code'),

				'googlemap' => array('label' => __('Google Map', NBS_LANG_CODE), 'icon' => 'fa-globe'),

				'subscriptionList' => array('label' => __('Subscription List', NBS_LANG_CODE), 'icon' => 'fa-th-list'),
			));
			$isPro = frameNbs::_()->getModule('supsystic_promo')->isPro();
			foreach($this->_fieldTypes as $code => $f) {
				if(isset($f['pro']) && !$isPro) {
					$this->_fieldTypes[ $code ]['pro'] = frameNbs::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=field_'. $code. '&utm_campaign=forms');;
				}
			}
		}
		return $this->_fieldTypes;
	}
	public function getFieldTypeByCode( $htmlCode ) {
		$this->getFieldTypes();
		return isset( $this->_fieldTypes[ $htmlCode ] ) ? $this->_fieldTypes[ $htmlCode ] : false;
	}
	public function isFieldListSupported( $htmlCode ) {
		return $htmlCode && in_array($htmlCode, array('subscriptionList','selectbox', 'selectlist', 'radiobuttons', 'checkboxlist'));
	}
	public function showForm($params) {
		$id = isset($params['id']) ? (int) $params['id'] : 0;
		if(!$id && isset($params[0]) && !empty($params[0])) {	// For some reason - for some cases it convert space in shortcode - to %20 im this place
			$id = explode('=', $params[0]);
			$id = isset($id[1]) ? (int) $id[1] : 0;
		}
		if($id) {
			$params['id'] = $id;
			return $this->getView()->showForm( $params );
		}
	}
	public function getAssetsforPrevStr() {
		$frontendStyles = $this->getView()->getFrontendStyles();
		$stylesStr = '';
		foreach($frontendStyles as $sKey => $sUrl) {
			$stylesStr .= '<link rel="stylesheet" href="'. $sUrl. '" type="text/css" media="all" />';
		}
		$stylesStr .= '<style type="text/css">
				.nbsFormPreloadImg {
					width: 1px !important;
					height: 1px !important;
					position: absolute !important;
					top: -9999px !important;
					left: -9999px !important;
					opacity: 0 !important;
				}
			</style>';
		return $stylesStr;
	}
}
