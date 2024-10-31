<?php
class formsViewNbs extends viewNbs {
	protected $_twig;
	private $_renderFormIter = 0;
	private $_lastForm = null;
	private $_saveLastForm = false;

	public function getTabContent() {
		frameNbs::_()->getModule('templates')->loadJqGrid();
		frameNbs::_()->addScript('admin.forms', $this->getModule()->getModPath(). 'js/admin.forms.js');
		frameNbs::_()->addScript('admin.forms.list', $this->getModule()->getModPath(). 'js/admin.forms.list.js');
		frameNbs::_()->addJSVar('admin.forms.list', 'nbsTblDataUrl', uriNbs::mod('forms', 'getListForTbl', array('reqType' => 'ajax')));
		
		$this->assign('addNewLink', frameNbs::_()->getModule('options')->getTabUrl('forms_add_new'));
		return parent::getContent('formsAdmin');
	}
	public function getAddNewTabContent() {
		frameNbs::_()->getModule('templates')->loadJqueryUi();
		frameNbs::_()->addStyle('admin.forms', $this->getModule()->getModPath(). 'css/admin.forms.css');
		frameNbs::_()->addScript('admin.forms', $this->getModule()->getModPath(). 'js/admin.forms.js');
		frameNbs::_()->getModule('templates')->loadMagicAnims();
		
		$changeFor = (int) reqNbs::getVar('change_for', 'get');
		//frameNbs::_()->addJSVar('admin.forms', 'nbsChangeFor', array($changeFor));
		if($changeFor) {
			$originalForm = $this->getModel()->getById( $changeFor );
			$editLink = $this->getModule()->getEditLink( $changeFor );
			$this->assign('originalForm', $originalForm);
			$this->assign('editLink', $editLink);
			frameNbs::_()->addJSVar('admin.forms', 'nbsOriginalForm', $originalForm);
			dispatcherNbs::addFilter('mainBreadcrumbs', array($this, 'modifyBreadcrumbsForChangeTpl'));
		}
		$this->assign('list', dispatcherNbs::applyFilters('showFormsTplsList', $this->getModel()
			->setOrderBy('sort_order')
			->setSortOrder('ASC')
			->getSimpleList(array('active' => 1, 'original_id' => 0))));
		$this->assign('changeFor', $changeFor);
		
		return parent::getContent('formsAddNewAdmin');
	}
	public function modifyBreadcrumbsForChangeTpl($crumbs) {
		$crumbs[ count($crumbs) - 1 ]['label'] = __('Modify Form Template', NBS_LANG_CODE);
		return $crumbs;
	}
	public function adminBreadcrumbsClassAdd() {
		echo ' supsystic-sticky';
	}
	public function getEditTabContent($id) {
		$form = $this->getModel()->getById($id);
		if(empty($form)) {
			return __('Cannot find required Form', NBS_LANG_CODE);
		}
		dispatcherNbs::doAction('beforeFormEdit', $form);
		
		dispatcherNbs::addAction('afterAdminBreadcrumbs', array($this, 'showEditFormFormControls'));
		dispatcherNbs::addAction('adminBreadcrumbsClassAdd', array($this, 'adminBreadcrumbsClassAdd'));
		if(empty($form['ab_id'])) {
			dispatcherNbs::addFilter('mainBreadcrumbs', array($this, 'changeMainBreadCrumbsClb'));
		}
		
		// !remove this!!!!
		//$form['params']['opts_attrs']['bg_number'] = 2;
		/*$form['params']['opts_attrs'] = array(
			'bg_number' => 4,
			'txt_block_number' => 1,
		);*/
		/*$form['params']['opts_attrs']['txt_block_number'] = 0;
		$form['params']['opts_attrs']['video_width_as_forms'] = 1;
		$form['params']['opts_attrs']['video_height_as_forms'] = 1;*/
		// !remove this!!!!
		if(!is_array($form['params']))
			$form['params'] = array();
		
		frameNbs::_()->getModule('templates')->loadJqueryUi();
		frameNbs::_()->getModule('templates')->loadSortable();
		frameNbs::_()->getModule('templates')->loadCodemirror();
		frameNbs::_()->getModule('templates')->loadBootstrapPartialOnlyCss();
		//frameNbs::_()->getModule('templates')->loadSerializeJson();
		if ( ! class_exists( '_WP_Editors', false ) )
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
		
		$fieldTypes = $this->getModule()->getFieldTypes();
		$nbsAddNewUrl = frameNbs::_()->getModule('options')->getTabUrl('forms_add_new');
		frameNbs::_()->addStyle('admin.forms', $this->getModule()->getModPath(). 'css/admin.forms.css');
		frameNbs::_()->addScript('admin.forms.fields', $this->getModule()->getModPath(). 'js/admin.forms.fields.js');
		frameNbs::_()->addJSVar('admin.forms.fields', 'nbsFormTypes', $fieldTypes);
		frameNbs::_()->addScript('admin.forms.submit', $this->getModule()->getModPath(). 'js/admin.forms.submit.js');
		frameNbs::_()->addScript('admin.forms', $this->getModule()->getModPath(). 'js/admin.forms.js');
		frameNbs::_()->addScript('admin.forms.edit', $this->getModule()->getModPath(). 'js/admin.forms.edit.js');
		frameNbs::_()->addJSVar('admin.forms.edit', 'nbsForm', $form);
		frameNbs::_()->addJSVar('admin.forms.edit', 'nbsAddNewUrl', $nbsAddNewUrl);
		
		frameNbs::_()->addScript('wp.tabs', NBS_JS_PATH. 'wp.tabs.js');
		
		$bgType = array(
			'none' => __('None', NBS_LANG_CODE),
			'img' => __('Image', NBS_LANG_CODE),
			'color' => __('Color', NBS_LANG_CODE),
		);

		$this->assign('csvExportUrl', uriNbs::mod('forms', 'exportCsv', array('id' => $id)));
		
		$this->assign('adminEmail', get_bloginfo('admin_email'));
		$this->assign('isPro', frameNbs::_()->getModule('supsystic_promo')->isPro());
		$this->assign('mainLink', frameNbs::_()->getModule('supsystic_promo')->getMainLink());
		$this->assign('promoModPath', frameNbs::_()->getModule('supsystic_promo')->getAssetsUrl());

		$this->assign('nbsAddNewUrl', $nbsAddNewUrl);
		$this->assign('bgTypes', $bgType);
		$this->assign('previewUrl', uriNbs::mod('forms', 'getPreviewHtml', array('id' => $id)));
		$this->assign('form', $form);
		$this->assign('fieldTypes', $fieldTypes);

		$this->assign('bgNames', $this->getModel()->getBgNamesForForm( $form['unique_id'] ));

		$tabs = array(
			'nbsFormTpl' => array(
				'title' => __('Design', NBS_LANG_CODE), 
				'content' => $this->getMainFormTplTab(),
				'fa_icon' => 'fa-picture-o',
				'sort_order' => 0),
			'nbsFormFields' => array(
				'title' => __('Fields', NBS_LANG_CODE), 
				'content' => $this->getMainFormFieldsTab(),
				'fa_icon' => 'fa-list',
				'sort_order' => 10),
			'nbsSubmitOpts' => array(
				'title' => __('Submit Options', NBS_LANG_CODE), 
				'content' => $this->getMainFormSubmitOptsTab(),
				'fa_icon' => 'fa-envelope-o',
				'sort_order' => 20),
			'nbsFormStatistics' => array(
				'title' => __('Statistics', NBS_LANG_CODE), 
				'content' => $this->getMainFormStatisticsOptsTab(),
				'fa_icon' => 'fa-line-chart',
				'sort_order' => 100,
			),
			'nbsFormEditors' => array(
				'title' => __('CSS / HTML Code', NBS_LANG_CODE), 
				'content' => $this->getMainFormCodeTab(),
				'fa_icon' => 'fa-code',
				'sort_order' => 999),
		);
		$tabs = dispatcherNbs::applyFilters('formsEditTabs', $tabs, $form);
		uasort($tabs, array($this, 'sortEditFormTabsClb'));
		$this->assign('tabs', $tabs);
		dispatcherNbs::doAction('beforeFormEditRender', $form);
		return parent::getContent('formsEditAdmin');
	}
	public function changeMainBreadCrumbsClb($crumbs) {
		return array( $crumbs[ count($crumbs) - 1 ] );	// Get rid of all other breadcrumbs - leave space on this page for other important things (buttons, etc.)
	}
	public function showEditFormFormControls() {
		$popupSupported = false;
		if(class_exists('framePps')) {	//PopUp is supported
			$this->assign('popupSelectUrl', framePps::_()->getModule('options')->getTabUrl('popup'));
			$popupSupported = true;
		}
		$this->assign('popupSupported', $popupSupported);
		parent::display('formsEditFormControls');
	}
	public function sortEditFormTabsClb($a, $b) {
		if($a['sort_order'] > $b['sort_order'])
			return 1;
		if($a['sort_order'] < $b['sort_order'])
			return -1;
		return 0;
	}
	public function getMainFormSubmitOptsTab() {
		$subLists = frameNbs::_()->getModule('subscribers_lists')->getModel()->getSimpleList();
		$subListsForSelect = array();
		if(!empty($subLists)) {
			foreach($subLists as $sl) {
				$subListsForSelect[ $sl['id'] ] = $sl['label'];
			}
		}
		$this->assign('subListsForSelect', $subListsForSelect);
		return parent::getContent('formsEditFormSubmitOpts');
	}
	public function getMainFormTplTab() {
		return parent::getContent('formsEditAdminTplOpts');
	}
	public function getMainFormFieldsTab() {
		$isGoogleMapsAvailable = class_exists('frameGmp');
		if($isGoogleMapsAvailable) {
			$allGoogleMaps = frameGmp::_()->getModule('gmap')->getModel()->getAllMaps(array('simple' => true));
			$allGoogleMapsForSelect = array();
			if(!empty($allGoogleMaps)) {
				foreach($allGoogleMaps as $m) {
					$allGoogleMapsForSelect[ $m['id'] ] = $m['title'];
				} 
			}
			$this->assign('allGoogleMapsForSelect', $allGoogleMapsForSelect);
		}
		$this->assign('isGoogleMapsAvailable', $isGoogleMapsAvailable);
		return parent::getContent('formsEditFormFields');
	}
	public function getMainFormStatisticsOptsTab() {
		frameNbs::_()->addScript('google.charts', 'https://www.gstatic.com/charts/loader.js');
		frameNbs::_()->getModule('templates')->loadJqGrid();
		frameNbs::_()->getModule('templates')->loadDatePicker();
		frameNbs::_()->addScript('admin.forms.statistics', $this->getModule()->getModPath(). 'js/admin.forms.statistics.js');

		if(!empty($this->form['views'])) {
			frameNbs::_()->addJSVar('admin.forms.statistics', 'nbsFormBaseStats', array(
				'views' => $this->form['views'],
				'unique_views' => $this->form['unique_views'],
				'actions' => $this->form['actions'],
			));
		}
		$proStatsHtml = '';
		if(frameNbs::_()->getModule('supsystic_promo')->isPro() 
			&& frameNbs::_()->getModule('forms_stats_pro')
		) {
			$proStatsHtml = frameNbs::_()->getModule('forms_stats_pro')->getView()->getFormGraphs( $this->form['id'] );
		}
		$this->assign('proStatsHtml', $proStatsHtml);
		return parent::getContent('formsEditFormStatistics');
	}
	public function getFrontendStyles() {
		return array(
			NBS_CODE. '.frontend.bootstrap.partial' => frameNbs::_()->getModule('forms')->getAssetsUrl(). 'css/frontend.bootstrap.partial.min.css',
			NBS_CODE. '.frontend.forms' => $this->getModule()->getModPath(). 'css/frontend.forms.css',
		);
	}
	public function showForm($params) {
		frameNbs::_()->getModule('templates')->loadCoreJs();
		$id = $params['id'];
		
		$id = dispatcherNbs::applyFilters('formIdBeforeShow', $id);
		
		$form = $this->getModel()->getById( $id );
		if(!empty($form)) {
			$form['connect_hash'] = md5(date('m-d-Y'). $id. NONCE_KEY);;
			$frontendStyles = $this->getFrontendStyles();
			foreach($frontendStyles as $sKey => $sUrl) {
				frameNbs::_()->addStyle($sKey, $sUrl);
			}
			frameNbs::_()->addScript(NBS_CODE. '.modernizr', $this->getModule()->getModPath(). 'js/forms.modernizr.min.js');
			frameNbs::_()->addScript(NBS_CODE. '.frontend.forms', $this->getModule()->getModPath(). 'js/frontend.forms.js');
			frameNbs::_()->addJSVar(NBS_CODE. '.frontend.forms', 'nbsForms_'. $this->_renderFormIter, $this->_prepareForFront($form));
			frameNbs::_()->addJSVar(NBS_CODE. '.frontend.forms', 'nbsFormsRenderFormIter', array('lastIter' => $this->_renderFormIter));
			$this->_renderFormIter++;
			$this->_checkLoadFieldsAssets( $form['params']['fields'] );
			if($this->_saveLastForm) {
				$this->_lastForm = $form;
			}
			return $this->generateHtml( $form );
		}
		return 'Can not find Form in database';
	}
	public function getLastForm() {
		return $this->_lastForm;
	}
	public function saveLastForm( $val ) {
		$this->_saveLastForm = $val;
	}
	/**
	 * Exclude unvanted for frontend data from form
	 * @param array $form Form data to be rendered
	 * @return array Form data without parameters for frontend
	 */
	private function _prepareForFront( $form ) {
		unset($form['css']);
		unset($form['html']);
		if(isset($form['params']['fields'])) {
			foreach($form['params']['fields'] as $i => $f) {
				if($f['html'] == 'recaptcha') {
					unset($form['params']['fields'][ $i ]['recap-sitekey']);
					unset($form['params']['fields'][ $i ]['recap-secret']);
				}
			}
		}
		$removeParamsKeys = array('sub_aweber_listname', 'sub_aweber_adtracking', 'sub_mailchimp_api_key', 'sub_mailchimp_lists', 'sub_ar_form_action',
			'sub_sga_id', 'sub_sga_list_id', 'sub_sga_activate_code', 'sub_gr_api_key', 'sub_ac_api_url', 'sub_ac_api_key', 
			'sub_ac_lists', 'sub_mr_lists', 'sub_gr_api_key', 'sub_gr_lists', 'cycle_day', 'sub_ic_app_id', 'sub_ic_app_user', 'sub_ic_app_pass', 'sub_ic_lists',
			'sub_ck_api_key', 'sub_mem_acc_id', 'sub_mem_pud_key', 'sub_mem_priv_key', 'test_email');
		foreach($removeParamsKeys as $unKey) {
			if(isset($form[ $i ]['params']['tpl'][ $unKey ]))
				unset($form[ $i ]['params']['tpl'][ $unKey ]);
		}
		return $form;
	}
	private function _checkLoadFieldsAssets( $fields ) {
		foreach($fields as $f) {
			switch( $f['html'] ) {
				case 'date': case 'month': case 'week':
					frameNbs::_()->getModule('templates')->loadDatePicker();
					frameNbs::_()->getModule('templates')->loadJqueryUi();
					break;
				case 'time':
					frameNbs::_()->getModule('templates')->loadTimePicker();
					break;
			}
		}
	}
	/*public function getMainFormSubTab() {
		frameNbs::_()->getModule('subscribe')->loadAdminEditAssets();
		//MailPoet check
		$mailPoetAvailable = class_exists('WYSIJA');
		if($mailPoetAvailable) {
			$mailPoetLists = WYSIJA::get('list', 'model')->get(array('name', 'list_id'), array('is_enabled' => 1));
			$mailPoetListsSelect = array();
			if(!empty($mailPoetLists)) {
				foreach($mailPoetLists as $l) {
					$mailPoetListsSelect[ $l['list_id'] ] = $l['name'];
				}
			}
			$this->assign('mailPoetListsSelect', $mailPoetListsSelect);
		}
		//Newsletter plugin check
		// Unavailable for now
		$newsletterAvailable = false;
		if($newsletterAvailable) {

		}
		//Jetpack plugin check
		$jetpackAvailable = class_exists('Jetpack');
		$this->assign('availableUserRoles', frameNbs::_()->getModule('subscribe')->getAvailableUserRolesForSelect());
		$this->assign('mailPoetAvailable', $mailPoetAvailable);
		$this->assign('newsletterAvailable', $newsletterAvailable);
		$this->assign('wpCsvExportUrl', uriNbs::mod('subscribe', 'getWpCsvList', array('id' => $this->forms['id'])));
		$this->assign('jetpackAvailable', $jetpackAvailable);
		return parent::getContent('formsEditAdminSubOpts');
	}
	public function getMainFormSmTab() {
		$sssPlugAvailable = class_exists('SupsysticSocialSharing');
		global $supsysticSocialSharing;
		if($sssPlugAvailable && isset($supsysticSocialSharing) && method_exists($supsysticSocialSharing, 'getEnvironment')) {
			$sssProjects = $supsysticSocialSharing->getEnvironment()->getModule('Projects')->getController()->getModelsFactory()->get('projects')->all();
			if(empty($sssProjects)) {
				$this->assign('addProjectUrl', $supsysticSocialSharing->getEnvironment()->generateUrl('projects'). '#add');
			} else {
				$sssProjectsForSelect = array(0 => __('None - use Standard Form Social Buttons'));
				$formsIdFound = false;
				foreach($sssProjects as $p) {
					$sssProjectsForSelect[ $p->id ] = $p->title;
					if(isset($p->settings) 
						&& isset($p->settings['forms_id']) 
						&& $p->settings['forms_id'] == $this->forms['id']
					) {
						if(!isset($this->forms['params']['tpl']['use_sss_prj_id'])) {
							$this->forms['params']['tpl']['use_sss_prj_id'] = $p->id;
						}
						$formsIdFound = true;
					}
				}
				if(!$formsIdFound 
					&& isset($this->forms['params']['tpl']['use_sss_prj_id']) 
					&& !empty($this->forms['params']['tpl']['use_sss_prj_id'])
				) {
					$this->forms['params']['tpl']['use_sss_prj_id'] = 0;
				}
			}
			$this->assign('sssProjectsForSelect', $sssProjectsForSelect);
		}
		$this->assign('sssPlugAvailable', $sssPlugAvailable);
		return parent::getContent('formsEditAdminSmOpts');
	}*/
	public function getMainFormCodeTab() {
		return parent::getContent('formsEditAdminCodeOpts');
	}
	public function adjustOpacity($color, $alpha) {
		$alpha = max(0, min(1, $alpha));
		$rgbColor = utilsNbs::hexToRgb( $color );
		$rgbColor[] = $alpha;
		return 'rgba('. implode(',', $rgbColor). ')';
	}
	public function adjustBrightness($hex, $steps) {
		 // Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max(-255, min(255, $steps));

		// Normalize into a six character long hex string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex, 0, 1), 2). str_repeat(substr($hex, 1, 1), 2). str_repeat(substr($hex, 2, 1), 2);
		}

		// Split into three parts: R, G and B
		$color_parts = str_split($hex, 2);
		$return = '#';

		foreach ($color_parts as $color) {
			$color   = hexdec($color); // Convert to decimal
			$color   = max(0, min(255, $color + $steps)); // Adjust color
			$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
		}

		return $return;
	}
	public function generateHtml($form, $params = array()) {
		$replaceStyleTag = isset($params['replace_style_tag']) ? $params['replace_style_tag'] : false;
		if(is_numeric($form)) {
			$form = $this->getModel()->getById($form);
		}
		$this->_initTwig();
		
		$form = dispatcherNbs::applyFilters('beforeFormRender', $form);

		$form['params']['tpl']['form_start'] = $this->generateFormStart( $form );
		$form['params']['tpl']['fields'] = $this->generateFields( $form );
		$form['params']['tpl']['form_end'] = $this->generateFormEnd( $form );
			
		$form['css'] .= $this->_generateCommonFormCss( $form );
		
		$form['css'] = $this->_replaceTagsWithTwig( $form['css'], $form );
		$form['html'] = $this->_replaceTagsWithTwig( $form['html'], $form );
		
		$form['html'] .= $this->_generateImgsPreload( $form );
		
		$form['css'] = dispatcherNbs::applyFilters('formCss', $form['css'], $form);
		$form['html'] = dispatcherNbs::applyFilters('formHtml', $form['html'], $form);
		// $replaceStyleTag can be used for compability with other plugins minify functionality: 
		// it will not recognize css in js data as style whye rendering on server side, 
		// but will be replaced back to normal <style> tag in JS, @see js/frontend.forms.js
		return $this->_twig->render(
				($replaceStyleTag ? '<span style="display: none;" id="nbsFormStylesHidden_'. $form['view_id']. '">' : '<style type="text/css" id="'. $form['view_html_id']. '_style">')
					. $form['css']
				. ($replaceStyleTag ? '</span>' : '</style>')
				. '<div id="'. $form['view_html_id']. '" class="nbsFormShell cfsFormShell">'. $form['html']. '</div>',	//cfsFormShell class added to leave compatibility with Contact Form styles - to avoid it's copying
			array('forms' => $form)
		);
	}
	private function _generateCommonFormCss( $form ) {
		$res = '';
		$res .= '#[SHELL_ID] { width: '. $form['params']['tpl']['width']. $form['params']['tpl']['width_measure']. '}';
		if(isset($form['params'], $form['params']['tpl'], $form['params']['tpl']['form_sent_msg_color'])
			&& !empty($form['params']['tpl']['form_sent_msg_color']) 
		) {
			$res .= '#[SHELL_ID] .nbsSuccessMsg { color: '. $form['params']['tpl']['form_sent_msg_color']. ' !important}';
		}
		return $res;
	}
	public function generateFormStart( $form ) {
		return '<form class="nbsForm" method="post" action="'. NBS_SITE_URL. '">';
	}
	public function generateFormEnd( $form ) {
		$res = '';
		$res .= htmlNbs::hidden('mod', array('value' => 'forms'));
		$res .= htmlNbs::hidden('action', array('value' => 'subscribe'));
		$res .= htmlNbs::hidden('id', array('value' => $form['id']));
		$res .= htmlNbs::hidden('_wpnonce', array('value' => wp_create_nonce('subscribe-'. $form['id'])));
		$res .= '<div class="nbsSubscribeMsg"></div>';
		$res .= '</form>';
		return $res;
	}
	/*private function _generateFieldClasses( $field ) {
		return '';
	}
	private function _generateFieldStyles( $field ) {
		return '';
	}*/
	public function generateField( $f, $form, $params = array() ) {
		$mod = isset($params['mod']) ? $params['mod'] : $this->getModule();
		$addFieldsMod = isset($params['addFieldsMod']) ? $params['addFieldsMod'] : frameNbs::_()->getModule('add_fields');
		$htmlType = isset($params['htmlType']) ? $params['htmlType'] : $f['html'];
		$fieldListSupported = isset($params['fieldListSupported']) ? $params['fieldListSupported'] : $mod->isFieldListSupported($htmlType);	
		$fieldWrapper = isset($params['fieldWrapper']) ? $params['fieldWrapper'] : $form['params']['tpl']['field_wrapper'];
		if(!isset($params['fieldWrapper'])) {
			if(strpos($fieldWrapper, '[field]') === false) {
				$fieldWrapper = '[field]';
			}
		}
		$name = isset($params['name']) ? $params['name'] : trim($f['name']);
		$fieldPrefName = isset($params['fieldPrefName']) ? $params['fieldPrefName'] : 'fields';
		
		$htmlParams = array('attrs' => '');
		$label = $f['label'];
		$placeholder = isset($f['placeholder']) ? $f['placeholder'] : '';
		
		$isButton = in_array($htmlType, array('submit', 'reset', 'button'));
		$isRadioCheckList = in_array($htmlType, array('radiobuttons', 'checkboxlist'));
		if($fieldListSupported && isset($f['options']) && !empty($f['options'])) {
			$htmlParams['options'] = array();
			foreach($f['options'] as $opt) {
				$htmlParams['options'][ $opt['name'] ] = isset($opt['label']) ? $opt['label'] : $opt['name'];
			}
		}
		if(!empty($placeholder)) {
			$htmlParams['placeholder'] = $placeholder;
		}
		if($isButton) {
			$f['value'] = $label;	// To not confuse user
		}
		if(isset($f['value']) && !empty($f['value'])) {
			if(isset($f['value_preset']) 
				&& $f['value_preset'] 
				&& $addFieldsMod 
				&& method_exists($addFieldsMod, 'generateValuePreset')
			) {
				$f['value'] = $addFieldsMod->generateValuePreset( $f['value_preset'] );
			}
			$htmlParams['value'] = $f['value'];
		}
		if(isset($f['mandatory']) && !empty($f['mandatory']) && (int)$f['mandatory']) {
			$htmlParams['required'] = true;
		}
		if(in_array($htmlType, array('checkbox'))) {
			$htmlParams['attrs'] .= 'style="height: auto; width: auto; margin: 0; padding: 0;"';
		}
		if(isset($f['display']) && !empty($f['display'])) {
			$htmlParams['display'] = $f['display'];
		}
		if(isset($f['min_size']) && !empty($f['min_size'])) {
			$htmlParams['min'] = $f['min_size'];
		}
		if(isset($f['max_size']) && !empty($f['max_size'])) {
			$htmlParams['max'] = $f['max_size'];
		}
		if(isset($f['add_classes']) && !empty($f['add_classes'])) {
			$htmlParams['attrs'] .= 'class="'. $f['add_classes']. '"';
		}
		if(isset($f['add_styles']) && !empty($f['add_styles'])) {
			$htmlParams['attrs'] .= ' style="'. $f['add_styles']. '"';
		}
		if(isset($f['add_attr']) && !empty($f['add_attr'])) {
			$htmlParams['attrs'] .= ' '. $f['add_attr'];
		}
		if(isset($f['vn_pattern']) && !empty($f['vn_pattern'])) {
			$htmlParams['pattern'] = $f['vn_pattern'];
		}
		if(isset($f['def_checked']) && !empty($f['def_checked'])) {
			$htmlParams['checked'] = 1;
		}
		if($htmlType == 'recaptcha') {
			foreach($f as $fParamKey => $fParam) {
				if(strpos($fParamKey, 'recap-') === 0 &&  strpos($fParamKey, 'secret') === false) {
					$htmlParams[ str_replace('recap-', '', $fParamKey) ] = $fParam;
				}
			}
		}

		// $isRadioCheckList fields have multiple labeled selections inside - them simply does not need main label selection
		// For all other - generate unique ID if it's required - here
		if(strpos($fieldWrapper, '[field_id]') !== false && !$isRadioCheckList) {	// Need field ID
			$id = htmlNbs::nameToClassId($name. mt_rand(1, 99999), $htmlParams);
			if(strpos($htmlParams['attrs'], $id) === false) {
				$htmlParams['attrs'] .= 'id="'. $id. '"';
			}
		}
		$fullName = $fieldPrefName. '['. $name. ']';
		$fieldTypeData = $this->getModule()->getFieldTypeByCode( $htmlType );
		if($fieldTypeData && isset($fieldTypeData['pro'])) {
			if(!$addFieldsMod) return;
			$inputHtml = $addFieldsMod->generateFieldHtml($htmlType, $fullName, $htmlParams, $form);
		} else {
			$inputHtml = htmlNbs::$htmlType($fullName, $htmlParams);
		}
		return $inputHtml;
	}
	public function generateFields( $form ) {
		$resHtml = '';
		if(isset($form['params']['fields']) && !empty($form['params']['fields'])) {
			$mod = $this->getModule();
			$fieldWrapper = $form['params']['tpl']['field_wrapper'];
			if(strpos($fieldWrapper, '[field]') === false) {
				$fieldWrapper = '[field]';
			}
			$fieldCommonClasses = 'nbsFieldShell';
			$rows = array();
			$addFieldsMod = frameNbs::_()->getModule('add_fields');	// it can be NULL!!!
			foreach($form['params']['fields'] as $f) {
				$htmlType = $f['html'];
				$name = trim($f['name']);
				$isHtmlDelim = $htmlType == 'htmldelim';
				$isGoogleMap = $htmlType == 'googlemap';
				if(empty($name) && !$isHtmlDelim && !$isGoogleMap) continue;
				
				$id = '';
				$insertLabelInternal = false;
				if($isHtmlDelim) {
					$inputHtml = $f['value'];
				} elseif($isGoogleMap) {
					$inputHtml = '';
					if(class_exists('frameGmp')) {
						$mapId = (int) $f['value'];
						if($mapId) {
							$inputHtml = frameGmp::_()->getModule('gmap')->drawMapFromShortcode(array('id' => $mapId));
						}
					} elseif(frameNbs::_()->getModule('user')->isAdmin()) {	// Show errors - only if user is admin, for usual visitors there will be just no field
						$inputHtml = sprintf(__('To use this field type you need to have installed and activated <a href="%s" target="_blank">Google Maps Easy</a> plugin - it\'s Free! Just install it <a class="button" target="_blank" href="%s">here.</a>', NBS_LANG_CODE), 'https://wordpress.org/plugins/google-maps-easy/', admin_url('plugin-install.php?tab=search&s=Google+Maps+Easy'));
					}
				} else {
					$fieldListSupported = $mod->isFieldListSupported($htmlType);
					// Additional html types, that does not have options selection, but will be displayed in same way as other lists
					$showAsList = $fieldListSupported || in_array($htmlType, array('countryList', 'countryListMultiple'));
					$showAsOneCheck = in_array($htmlType, array('checkbox', 'radiobutton'));
					$isButton = in_array($htmlType, array('submit', 'reset', 'button'));
					$isRadioCheckList = in_array($htmlType, array('radiobuttons', 'checkboxlist'));
					$label = $f['label'];
					
					$inputHtml = $this->generateField($f, $form, array(
						'mod' => $mod,
						'addFieldsMod' => $addFieldsMod,
						'htmlType' => $htmlType,
						'fieldListSupported' => $fieldListSupported,
						'fieldWrapper' => $fieldWrapper,
						'name' => $name,
					));
										
					$insertLabelInternal = strpos($fieldWrapper, '[label]') === false;
					if($showAsList) {
						$baseListTag = $isRadioCheckList ? 'span' : 'label';
						$txtLabel = $insertLabelInternal ? '<span class="nbsListSelectLabel">'. $label. ': </span>' : '';
						$inputHtml = '<'. $baseListTag. ' class="nbsListSelect">'. $txtLabel. $inputHtml. '</'. $baseListTag. '>';
					} elseif($showAsOneCheck) {
						$txtLabel = $insertLabelInternal ? '&nbsp;'. $label : '';
						$inputHtml = '<label class="nbsCheck">'. $inputHtml. $txtLabel. '</label>';
					} elseif(!$isButton && !empty($label)) {
						$txtLabel = $insertLabelInternal ? '<span class="nbsInputLabel">'. $label. '&nbsp;</span>' : '';
						$inputHtml = '<label>'. $txtLabel. $inputHtml. '</label>';
					}
					// Wrap it
					$classes = array($fieldCommonClasses);
					$replaceFrom = array('[field]', '[field_shell_classes]', '[field_shell_styles]', '[field_html]', '[field_id]');
					$replaceTo = array($inputHtml, 'class="'. implode(' ', $classes). '"', '', $htmlType, $id);
					if(!$insertLabelInternal) {
						$replaceFrom[] = '[label]';
						$replaceTo[] = $isButton ? '' : $label;
					}

					$inputHtml = str_replace(
						$replaceFrom, 
						$replaceTo, 
						$fieldWrapper);
				}
				$bsClassId = isset($f['bs_class_id']) && !empty($f['bs_class_id']) ? (int) $f['bs_class_id'] : 12;
				$inputHtml = '<div class="col-sm-'. $bsClassId. ' nbsFieldCol">'. $inputHtml. '</div>';	// Bootstrap col wrapper
				$added = false;
				if($bsClassId < 12) {	// Try to add it to prev. row
					$prevRowI = count( $rows ) - 1;
					if($prevRowI >= 0) {
						if($rows[ $prevRowI ]['id'] < 12) {
							$rows[ $prevRowI ]['id'] += $bsClassId;
							$rows[ $prevRowI ]['cols'][] = $inputHtml;
							$added = true;
						}
					}
				}
				if(!$added) {	// New row
					$rows[] = array('id' => $bsClassId, 'cols' => array( $inputHtml ));
				}
			}
			foreach($rows as $r) {
				$resHtml .= '<div class="row nbsFieldsRow">'. implode('', $r['cols']). '</div>';
			}
		}
		return $resHtml;
	}
	private function _generateImgsPreload( $form ) {
		$res = '';
		if(isset($form['params']['opts_attrs']['bg_number']) && !empty($form['params']['opts_attrs']['bg_number'])) {
			for($i = 0; $i < $form['params']['opts_attrs']['bg_number']; $i++) {
				if($form['params']['tpl']['bg_type_'. $i] == 'img' && !empty($form['params']['tpl']['bg_img_'. $i])) {
					$res .= '<img class="nbsFormPreloadImg nbsFormPreloadImg_'. $form['view_id']. '" src="'. $form['params']['tpl']['bg_img_'. $i]. '" />';
				}
			}
		}
		return $res;
	}
	private function _replaceTagsWithTwig($string, $form) {
		$string = preg_replace('/\[if (.+)\]/iU', '{% if forms.params.tpl.$1 %}', $string);
		$string = preg_replace('/\[elseif (.+)\]/iU', '{% elseif forms.params.tpl.$1 %}', $string);
		
		$replaceFrom = array('SHELL_ID', 'ID', 'endif', 'else');
		$replaceTo = array($form['view_html_id'], $form['view_id'], '{% endif %}', '{% else %}');
		// Standard shortcode processor didn't worked for us here - as it is developed for posts, 
		// not for direct "do_shortcode" call, so we created own embed shortcode processor
		if(isset($form['params']) && isset($form['params']['tpl'])) {
			foreach($form['params']['tpl'] as $key => $val) {
				if(is_array($val)) {
					foreach($val as $key2 => $val2) {
						if(is_array($val2)) {
							foreach($val2 as $key3 => $val3) {
								// Here should be some recursive and not 3 circles, but have not time for this right now, maybe you will do this?:)
								if(is_array($val3)) continue;
								$replaceFrom[] = $key. '_'. $key2. '_'. $key3;
								$replaceTo[] = $val3;
							}
						} else {
							$replaceFrom[] = $key. '_'. $key2;
							$replaceTo[] = $val2;
						}
					}
				} else {
					// Do shortcodes for all text type data in forms
					if(strpos($key, 'txt_') === 0 || strpos($key, 'label') === 0 || strpos($key, 'foot_note')) {
						$val = do_shortcode( $val );
					}
					$replaceFrom[] = $key;
					$replaceTo[] = $val;
				}
			}
		}
		foreach($replaceFrom as $i => $v) {
			$replaceFrom[ $i ] = '['. $v. ']';
		}
		return str_replace($replaceFrom, $replaceTo, $string);
	}
	protected function _initTwig() {
		if(!$this->_twig) {
			if(!class_exists('Twig_Autoloader')) {
				require_once(NBS_CLASSES_DIR. 'Twig'. DS. 'Autoloader.php');
			}
			Twig_Autoloader::register();
			$this->_twig = new Twig_Environment(new Twig_Loader_String(), array('debug' => 0));
			$this->_twig->addFunction(
				new Twig_SimpleFunction('adjust_brightness', array(
						$this,
						'adjustBrightness'
					)
				)
			);
			$this->_twig->addFunction(
				new Twig_SimpleFunction('adjust_opacity', array(
						$this,
						'adjustOpacity'
					)
				)
			);
		}
	}
}
