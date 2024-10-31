<?php
class optionsNbs extends moduleNbs {
	private $_tabs = array();
	private $_options = array();
	private $_optionsToCategoires = array();	// For faster search

	public function init() {
		//dispatcherNbs::addAction('afterModulesInit', array($this, 'initAllOptValues'));
		add_action('init', array($this, 'initAllOptValues'), 99);	// It should be init after all languages was inited (frame::connectLang)
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function initAllOptValues() {
		// Just to make sure - that we loaded all default options values
		$this->getAll();
	}
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($code) {
        return $this->getModel()->get($code);
    }
	/**
     * This method provides fast access to options model method save
     * @see optionsModel::save($d)
     */
	public function save($optKey, $val, $ignoreDbUpdate = false) {
		return $this->getModel()->save($optKey, $val, $ignoreDbUpdate);
	}
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($code) {
		return $this->getModel()->isEmpty($code);
	}
	public function getAllowedPublicOptions() {
		$allowKeys = array('add_love_link', 'disable_autosave');
		$res = array();
		foreach($allowKeys as $k) {
			$res[ $k ] = $this->get($k);
		}
		return $res;
	}
	public function getAdminPage() {
		if(installerNbs::isUsed()) {
			return $this->getView()->getAdminPage();
		} else {
			installerNbs::setUsed();	// Show this welcome page - only one time
			return frameNbs::_()->getModule('supsystic_promo')->showWelcomePage();
		}
	}
	public function addAdminTab($tabs) {
		$tabs['settings'] = array(
			'label' => __('Settings', NBS_LANG_CODE), 'callback' => array($this, 'getSettingsTabContent'), 'fa_icon' => 'fa-gear', 'sort_order' => 100,
		);
		return $tabs;
	}
	public function getSettingsTabContent() {
		return $this->getView()->getSettingsTabContent();
	}
	public function getTabs() {
		if(empty($this->_tabs)) {
			$this->_tabs = dispatcherNbs::applyFilters('mainAdminTabs', array(
				//'main_page' => array('label' => __('Main Page', NBS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'wp_icon' => 'dashicons-admin-home', 'sort_order' => 0),
			));
			foreach($this->_tabs as $tabKey => $tab) {
				if(!isset($this->_tabs[ $tabKey ]['url'])) {
					$this->_tabs[ $tabKey ]['url'] = $this->getTabUrl( $tabKey );
				}
			}
			uasort($this->_tabs, array($this, 'sortTabsClb'));
		}
		return $this->_tabs;
	}
	public function sortTabsClb($a, $b) {
		if(isset($a['sort_order']) && isset($b['sort_order'])) {
			if($a['sort_order'] > $b['sort_order'])
				return 1;
			if($a['sort_order'] < $b['sort_order'])
				return -1;
		}
		return 0;
	}
	public function getTab($tabKey) {
		$this->getTabs();
		return isset($this->_tabs[ $tabKey ]) ? $this->_tabs[ $tabKey ] : false;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getActiveTab() {
		$reqTab = reqNbs::getVar('tab');
		return empty($reqTab) ? 'newsletters' : $reqTab;
	}
	public function getTabUrl($tab = '', $hash = '') {
		static $mainUrl;
		if(empty($mainUrl)) {
			$mainUrl = frameNbs::_()->getModule('adminmenu')->getMainLink();
		}
		$url = empty($tab) ? $mainUrl : $mainUrl. '&tab='. $tab;
		if(!empty( $hash )) {
			$url .= '#'. $hash;
		}
		return $url;
	}
	public function getRolesList() {
		if(!function_exists('get_editable_roles')) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}
		return get_editable_roles();
	}
	public function getAvailableUserRolesSelect() {
		$rolesList = $this->getRolesList();
		$rolesListForSelect = array();
		foreach($rolesList as $rKey => $rData) {
			$rolesListForSelect[ $rKey ] = $rData['name'];
		}
		return $rolesListForSelect;
	}
	public function getAll() {
		if(empty($this->_options)) {
			$defSendmailPath = @ini_get('sendmail_path');
			 if (empty($defSendmailPath) && !stristr($defSendmailPath, 'sendmail')) {
				$defSendmailPath = '/usr/sbin/sendmail';
			}
			$this->_options = dispatcherNbs::applyFilters('optionsDefine', array(
				'general' => array(
					'label' => __('General', NBS_LANG_CODE),
					'opts' => array(
						'subscribers_login_email_sbj' => array('label' => __('Subscribers Login email Subject', NBS_LANG_CODE), 'desc' => esc_html(__('Subject for email message that will be sent to subscribers when they will try to login using our login form.', NBS_LANG_CODE)),
							'def' => __('Edit Subscription settings', NBS_LANG_CODE), 'html' => 'text'),
						'subscribers_login_email_txt' => array('label' => __('Subscribers Login email Text', NBS_LANG_CODE), 'desc' => esc_html(__('Email message text that will be sent to subscribers when they will try to login using our login form. You can use here variables [subscriber_login_url] - to define login URL.', NBS_LANG_CODE)),
							'def' => __('You can login to your Subscription profile using <a target="_blank" href="[subscriber_login_url]">this link</a>.', NBS_LANG_CODE), 'html' => 'textarea'),

						'subscribers_confirm_email_sbj' => array('label' => __('Subscribers Confirm email Subject', NBS_LANG_CODE), 'desc' => esc_html(__('Subject for email message that will be sent to subscribers for email confirmation.', NBS_LANG_CODE)),
							'def' => __('Edit Confirm', NBS_LANG_CODE), 'html' => 'text'),
						'subscribers_confirm_email_txt' => array('label' => __('Subscribers Confirm email Text', NBS_LANG_CODE), 'desc' => esc_html(__('Email message text that will be sent to subscribers for email confirmation. You can use here variables [subscriber_confirm_url] - to define confirm URL, [user_username] - for subscriber username, [user_email] - for subscriber email, [siteurl] - for site URL, [sitename] - your site name.', NBS_LANG_CODE)),
							'def' => __('Hello!<br />Your email [user_email] was used on site <a href="[siteurl] target="_blank">[sitename]</a> for subscription. Click on <a href="[subscriber_confirm_url]" target="_blank">following URL</a>to finish subscrption process.', NBS_LANG_CODE), 'html' => 'textarea'),

						'mail_send_engine' => array('label' => __('Send With', NBS_LANG_CODE), 'desc' => __('You can send your emails with different email sending engines.', NBS_LANG_CODE), 'def' => 'wp_mail', 'html' => 'selectbox',
							'options' => array('wp_mail' => __('WordPress PHP Mail', NBS_LANG_CODE), 'smtp' => __('Third party providers (SMTP)', NBS_LANG_CODE), 'sendmail' => __('Sendmail', NBS_LANG_CODE))),

						'smtp_host' => array('label' => __('SMTP Hostname', NBS_LANG_CODE), 'desc' => __('e.g. smtp.mydomain.com', NBS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:smtp'),
						'smtp_login' => array('label' => __('SMTP Login', NBS_LANG_CODE), 'desc' => __('Your email login', NBS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:smtp'),
						'smtp_pass' => array('label' => __('SMTP Password', NBS_LANG_CODE), 'desc' => __('Your emaail password', NBS_LANG_CODE), 'html' => 'password', 'connect' => 'mail_send_engine:smtp'),
						'smtp_port' => array('label' => __('SMTP Port', NBS_LANG_CODE), 'desc' => __('Port for your SMTP provider', NBS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:smtp'),
						'smtp_secure' => array('label' => __('SMTP Secure', NBS_LANG_CODE), 'desc' => __('Use secure SMTP connection. If you enable this option - make sure that your server support such secure connections.', NBS_LANG_CODE), 'html' => 'selectbox', 'connect' => 'mail_send_engine:smtp',
							'options' => array('' => __('No', NBS_LANG_CODE), 'ssl' => 'SSL', 'tls' => 'TLS'), 'def' => ''),

						'sendmail_path' => array('label' => __('Sendmail Path', NBS_LANG_CODE), 'desc' => __('You can check it on your server, or ask about it - in your hosting provider.', NBS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:sendmail', 'def' => $defSendmailPath),

						'send_engine_test' => array('label' => __('Test selected send engine', NBS_LANG_CODE), 'desc' => __('Just to check that selected sending method work - you can send test email here', NBS_LANG_CODE), 'html' => 'email', 'def' => get_bloginfo('admin_email')),

						'send_stats' => array('label' => __('Send usage statistics', NBS_LANG_CODE), 'desc' => __('Send information about what plugin options you prefer to use, this will help us make our solution better for You.', NBS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'add_love_link' => array('label' => __('Enable promo link', NBS_LANG_CODE), 'desc' => __('We are trying to make our plugin better for you, and you can help us with this. Just check this option - and small promotion link will be added in the bottom of your Newsletter. This is easy for you - but very helpful for us!', NBS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'access_roles' => array('label' => __('User role can use plugin', NBS_LANG_CODE), 'desc' => __('User with next roles will have access to whole plugin from admin area.', NBS_LANG_CODE), 'def' => 'administrator', 'html' => 'selectlist', 'options' => array($this, 'getAvailableUserRolesSelect'), 'pro' => ''),
						'disable_email_html_type' => array('label' => __('Disable HTML Emails content type', NBS_LANG_CODE), 'desc' => __('Some servers fail send emails with HTML content type: content-type = "text/html", so if you have problems with sending emails from our plugn - try to disable this feature here.', NBS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						//'use_local_cdn' => array('label' => __('Disable CDN usage', NBS_LANG_CODE), 'desc' => esc_html(sprintf(__('By defaul our plugin is using CDN server to store there part of it\'s files - images, javascript and CSS libraries. This was designed in that way to reduce plugin size, make it lighter and easier for usage. But if you need to store all files - on your server - you can disable this option here, then upload plugin CDN files to your own site like described in <a href="%s" target="_blank">this article</a>.', NBS_LANG_CODE), '#')), 'def' => '0', 'html' => 'checkboxHiddenVal'),
					),
				),
			));
			$isPro = frameNbs::_()->getModule('supsystic_promo')->isPro();
			foreach($this->_options as $catKey => $cData) {
				foreach($cData['opts'] as $optKey => $opt) {
					$this->_optionsToCategoires[ $optKey ] = $catKey;
					if(isset($opt['pro']) && !$isPro) {
						$this->_options[ $catKey ]['opts'][ $optKey ]['pro'] = frameNbs::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium='. $optKey. '&utm_campaign=popup');
					}
				}
			}
			$this->getModel()->fillInValues( $this->_options );
		}
		return $this->_options;
	}
	public function getFullCat($cat) {
		$this->getAll();
		return isset($this->_options[ $cat ]) ? $this->_options[ $cat ] : false;
	}
	public function getCatOpts($cat) {
		$opts = $this->getFullCat($cat);
		return $opts ? $opts['opts'] : false;
	}
}
