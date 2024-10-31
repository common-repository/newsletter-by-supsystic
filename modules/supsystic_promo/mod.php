<?php
class supsystic_promoNbs extends moduleNbs {
	private $_mainLink = '';
	private $_minDataInStatToSend = 20;	// At least 20 points in table shuld be present before send stats
	private $_assetsUrl = '';
	public function __construct($d) {
		parent::__construct($d);
		$this->getMainLink();
		dispatcherNbs::addFilter('jsInitVariables', array($this, 'addMainOpts'));
	}
	public function init() {
		parent::init();
		add_action('admin_footer', array($this, 'displayAdminFooter'), 9);
		if(is_admin()) {
			add_action('init', array($this, 'checkWelcome'));
			add_action('init', array($this, 'checkStatisticStatus'));
		}
		$this->weLoveYou();
		dispatcherNbs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherNbs::addAction('beforeSaveOpts', array($this, 'checkSaveOpts'));
		dispatcherNbs::addFilter('showTplsList', array($this, 'checkProTpls'));
		dispatcherNbs::addAction('discountMsg', array($this, 'getDiscountMsg'));
		add_action('admin_notices', array($this, 'checkAdminPromoNotices'));
		// Admin tutorial
		add_action('admin_enqueue_scripts', array( $this, 'loadTutorial'));
	}
	public function checkAdminPromoNotices() {
		if(!frameNbs::_()->isAdminPlugOptsPage())	// Our notices - only for our plugin pages for now
			return;
		$notices = array();
		// Start usage
		$startUsage = (int) frameNbs::_()->getModule('options')->get('start_usage');
		$currTime = time();
		$day = 24 * 3600;
		if($startUsage) {	// Already saved
			$rateMsg = sprintf(__("<h3>Hey, I noticed you just use %s over a week – that’s awesome!</h3><p>Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.</p>", NBS_LANG_CODE), NBS_WP_PLUGIN_NAME);
			$rateMsg .= '<p><a href="https://wordpress.org/support/view/plugin-reviews/newsletters-by-supsystic?rate=5#postform" target="_blank" class="button button-primary" data-statistic-code="done">'. __('Ok, you deserve it', NBS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="later">'. __('Nope, maybe later', NBS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="hide">'. __('I already did', NBS_LANG_CODE). '</a></p>';
			$enbPromoLinkMsg = sprintf(__("<h3>More then eleven days with our %s plugin - Congratulations!</h3>", NBS_LANG_CODE), NBS_WP_PLUGIN_NAME);;
			$enbPromoLinkMsg .= __('<p>On behalf of the entire <a href="https://supsystic.com/" target="_blank">supsystic.com</a> company I would like to thank you for been with us, and I really hope that our software helped you.</p>', NBS_LANG_CODE);
			$enbPromoLinkMsg .= __('<p>And today, if you want, - you can help us. This is really simple - you can just add small promo link to our site under your Forms. This is small step for you, but a big help for us! Sure, if you don\'t want - just skip this and continue enjoy our software!</p>', NBS_LANG_CODE);
			$enbPromoLinkMsg .= '<p><a href="#" class="button button-primary" data-statistic-code="done">'. __('Ok, you deserve it', NBS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="later">'. __('Nope, maybe later', NBS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="hide">'. __('Skip', NBS_LANG_CODE). '</a></p>';
			$notices = array(
				'rate_msg' => array('html' => $rateMsg, 'show_after' => 7 * $day),
				'enb_promo_link_msg' => array('html' => $enbPromoLinkMsg, 'show_after' => 11 * $day),
			);
			foreach($notices as $nKey => $n) {
				if($currTime - $startUsage <= $n['show_after']) {
					unset($notices[ $nKey ]);
					continue;
				}
				$done = (int) frameNbs::_()->getModule('options')->get('done_'. $nKey);
				if($done) {
					unset($notices[ $nKey ]);
					continue;
				}
				$hide = (int) frameNbs::_()->getModule('options')->get('hide_'. $nKey);
				if($hide) {
					unset($notices[ $nKey ]);
					continue;
				}
				$later = (int) frameNbs::_()->getModule('options')->get('later_'. $nKey);
				if($later && ($currTime - $later) <= 2 * $day) {	// remember each 2 days
					unset($notices[ $nKey ]);
					continue;
				}
				if($nKey == 'enb_promo_link_msg' && (int)frameNbs::_()->getModule('options')->get('add_love_link')) {
					unset($notices[ $nKey ]);
					continue;
				}
			}
		} else {
			frameNbs::_()->getModule('options')->getModel()->save('start_usage', $currTime);
		}
		if(!empty($notices)) {
			if(isset($notices['rate_msg']) && isset($notices['enb_promo_link_msg']) && !empty($notices['enb_promo_link_msg'])) {
				unset($notices['rate_msg']);	// Show only one from those messages
			}
			$html = '';
			foreach($notices as $nKey => $n) {
				$this->getModel()->saveUsageStat($nKey. '.'. 'show', true);
				$html .= '<div class="updated notice is-dismissible supsystic-admin-notice" data-code="'. $nKey. '">'. $n['html']. '</div>';
			}
			echo $html;
		}
	}
	public function addAdminTab($tabs) {
		$tabs['overview'] = array(
			'label' => __('Overview', NBS_LANG_CODE), 'callback' => array($this, 'getOverviewTabContent'), 'fa_icon' => 'fa-info', 'sort_order' => 5,
		);
		$tabs['featured-plugins'] = array(
			'label' => __('Featured Plugins', NBS_LANG_CODE), 'callback' => array($this, 'showFeaturedPluginsPage'), 'fa_icon' => 'fa-heart', 'sort_order' => 99,
		);
		return $tabs;
	}
	public function addSubDestList($subDestList) {
		if(!$this->isPro()) {
			$subDestList = array_merge($subDestList, array(
				'constantcontact' => array('label' => __('Constant Contact - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'campaignmonitor' => array('label' => __('Campaign Monitor - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'verticalresponse' => array('label' => __('Vertical Response - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'sendgrid' => array('label' => __('SendGrid - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'get_response' => array('label' => __('GetResponse - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'icontact' => array('label' => __('iContact - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'activecampaign' => array('label' => __('Active Campaign - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'mailrelay' => array('label' => __('Mailrelay - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'arpreach' => array('label' => __('arpReach - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'sgautorepondeur' => array('label' => __('SG Autorepondeur - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'benchmarkemail' => array('label' => __('Benchmark - PRO', NBS_LANG_CODE), 'require_confirm' => true),
				'infusionsoft' => array('label' => __('InfusionSoft - PRO', NBS_LANG_CODE), 'require_confirm' => false),
				'salesforce' => array('label' => __('SalesForce - Web-to-Lead - PRO', NBS_LANG_CODE), 'require_confirm' => false),
				'convertkit' => array('label' => __('ConvertKit - PRO', NBS_LANG_CODE), 'require_confirm' => false),
				'myemma' => array('label' => __('Emma - PRO', NBS_LANG_CODE), 'require_confirm' => false),
			));
		}
		return $subDestList;
	}
	public function getOverviewTabContent() {
		return $this->getView()->getOverviewTabContent();
	}
	public function showWelcomePage() {
		$this->getView()->showWelcomePage();
	}
	public function displayAdminFooter() {
		if(frameNbs::_()->isAdminPlugPage()) {
			$this->getView()->displayAdminFooter();
		}
	}
	private function _preparePromoLink($link, $ref = '') {
		if(empty($ref))
			$ref = 'user';
		return $link;
	}
	public function weLoveYou() {
		if(!$this->isPro()) {
			// Do nothing for now
		}
	}
	public function showAdditionalmainAdminShowOnOptions($newsletters) {
		$this->getView()->showAdditionalmainAdminShowOnOptions($newsletters);
	}
	/**
	 * Public shell for private method
	 */
	public function preparePromoLink($link, $ref = '') {
		return $this->_preparePromoLink($link, $ref);
	}
	public function checkStatisticStatus(){
		$canSend = (int) frameNbs::_()->getModule('options')->get('send_stats');
		if($canSend && frameNbs::_()->getModule('user')->isAdmin()) {
			// Before this version we had many wrong data collected taht we don't need at all. Let's clear them.
			if(NBS_VERSION == '1.3.5') {
				$clearedTrashStatData = (int) get_option(NBS_DB_PREF. 'cleared_trash_stat_data');
				if(!$clearedTrashStatData) {
					$this->getModel()->clearUsageStat();
					update_option(NBS_DB_PREF. 'cleared_trash_stat_data', 1);
					return;	// We just cleared whole data - so don't need to even check send stats
				}
			}
			$this->getModel()->checkAndSend();
		}
	}
	public function getMinStatSend() {
		return $this->_minDataInStatToSend;
	}
	public function getMainLink() {
		if(empty($this->_mainLink)) {
			$affiliateQueryString = '';
			$this->_mainLink = 'http://supsystic.com/plugins/newsletter-plugin/' . $affiliateQueryString;
		}
		return $this->_mainLink ;
	}
	public function generateMainLink($params = '') {
		$mainLink = $this->getMainLink();
		if(!empty($params)) {
			return $mainLink. (strpos($mainLink , '?') ? '&' : '?'). $params;
		}
		return $mainLink;
	}
	public function getContactFormFields() {
		$fields = array(
            'name' => array('label' => __('Name', NBS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
			'email' => array('label' => __('Email', NBS_LANG_CODE), 'html' => 'email', 'valid' => array('notEmpty', 'email'), 'placeholder' => 'example@mail.com', 'def' => get_bloginfo('admin_email')),
			'website' => array('label' => __('Website', NBS_LANG_CODE), 'html' => 'text', 'placeholder' => 'http://example.com', 'def' => get_bloginfo('url')),
			'subject' => array('label' => __('Subject', NBS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
            'category' => array('label' => __('Topic', NBS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'selectbox', 'options' => array(
				'plugins_options' => __('Plugin options', NBS_LANG_CODE),
				'bug' => __('Report a bug', NBS_LANG_CODE),
				'functionality_request' => __('Require a new functionality', NBS_LANG_CODE),
				'other' => __('Other', NBS_LANG_CODE),
			)),
			'message' => array('label' => __('Message', NBS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'textarea', 'placeholder' => __('Hello Supsystic Team!', NBS_LANG_CODE)),
        );
		foreach($fields as $k => $v) {
			if(isset($fields[ $k ]['valid']) && !is_array($fields[ $k ]['valid']))
				$fields[ $k ]['valid'] = array( $fields[ $k ]['valid'] );
		}
		return $fields;
	}
	public function isPro() {
		static $isPro;
		if(is_null($isPro)) {
			// license is always active with PRO - even if license key was not entered, 
			// add_options module was from the begining of the times in PRO, and will be active only once user will activate license on site
			$isPro = frameNbs::_()->getModule('license') && frameNbs::_()->getModule('stats_pro');
		}
		return $isPro;
	}
	public function getAssetsUrl() {
		if(empty($this->_assetsUrl)) {
			$this->_assetsUrl = frameNbs::_()->getModule('newsletters')->getAssetsUrl(). 'promo/';
		}
		return $this->_assetsUrl;
	}
	public function checkWelcome() {
		$from = reqNbs::getVar('from', 'get');
		$pl = reqNbs::getVar('pl', 'get');
		if($from == 'welcome-page' && $pl == NBS_CODE && frameNbs::_()->getModule('user')->isAdmin()) {
			$welcomeSent = (int) get_option(NBS_DB_PREF. 'welcome_sent');
			if(!$welcomeSent) {
				$this->getModel()->welcomePageSaveInfo();
				update_option(NBS_DB_PREF. 'welcome_sent', 1);
			}
			$skipTutorial = (int) reqNbs::getVar('skip_tutorial', 'get');
			if($skipTutorial) {
				$tourHst = $this->getModel()->getTourHst();
				$tourHst['closed'] = 1;
				$this->getModel()->setTourHst( $tourHst );
			}
		}
	}
	public function getContactLink() {
		return $this->getMainLink(). '#contact';
	}
	public function addMainOpts($opts) {
		$title = 'WordPress Newsletters Plugin';
		$opts['options']['love_link_html'] = '<a title="'. $title. '" style="color: #26bfc1 !important; font-size: 9px; position: absolute; bottom: 15px; right: 15px;" href="'. $this->generateMainLink('utm_source=plugin&utm_medium=love_link&utm_campaign=newsletters'). '" target="_blank">'
			. $title
			. '</a>';
		return $opts;
	}
	public function checkSaveOpts($newValues) {
		$loveLinkEnb = (int) frameNbs::_()->getModule('options')->get('add_love_link');
		$loveLinkEnbNew = isset($newValues['opt_values']['add_love_link']) ? (int) $newValues['opt_values']['add_love_link'] : 0;
		if($loveLinkEnb != $loveLinkEnbNew) {
			$this->getModel()->saveUsageStat('love_link.'. ($loveLinkEnbNew ? 'enb' : 'dslb'));
		}
	}
	public function checkProTpls($list) {
		if(!$this->isPro()) {
			$imgsPath = frameNbs::_()->getModule('newsletters')->getAssetsUrl(). 'img/tpl_prev/';
			$promoList = array(
				array('label' => 'Black & White', 'img' => 'black_and_white_tpl.png'),
				array('label' => 'Restaurant', 'img' => 'restaurant_tpl.png'),
				array('label' => 'Notification', 'img' => 'notification_tpl.png'),
				array('label' => 'Event', 'img' => 'event_tpl.png'),
				array('label' => 'Invoice', 'img' => 'invoice_tpl.png'),
				array('label' => 'Veggy', 'img' => 'veggy_tpl.png'),
			);

			foreach($promoList as $i => $t) {
				$promoList[ $i ]['is_pro'] = 1;
				$promoList[ $i ]['img_preview_url'] = $imgsPath. $promoList[ $i ]['img'];
				$promoList[ $i ]['promo'] = strtolower(str_replace(array(' ', '!', '&'), '', $t['label']));
				$promoList[ $i ]['promo_link'] = $this->generateMainLink('utm_source=plugin&utm_medium='. $promoList[ $i ]['promo']. '&utm_campaign=newsletters');
			}
			foreach($list as $i => $t) {
				if(isset($t['is_pro']) && (int)$t['is_pro']) {
					unset($list[ $i ]);
				}
			}
			$list = array_merge($list, $promoList);
		}
		return $list;
	}
	public function loadTutorial() {
		return;	// No tutorial for now
		// Don't run on WP < 3.3
		if ( get_bloginfo( 'version' ) < '3.3' )
			return;
		
		if ( is_admin() && current_user_can(frameNbs::_()->getModule('adminmenu')->getMainCap()) ) {
			
			$this->checkToShowTutorial();
        }
	}
	public function checkToShowTutorial() {
		if(reqNbs::getVar('tour', 'get') == 'clear-hst') {
			$this->getModel()->clearTourHst();
		}
		$hst = $this->getModel()->getTourHst();
		if((isset($hst['closed']) && $hst['closed']) 
			|| (isset($hst['finished']) && $hst['finished'])
		) {
			return;
		}
		$tourData = array();
		$tourData['tour'] = array(
			'welcome' => array(
				'points' => array(
					'first_welcome' => array(
						'target' => '#toplevel_page_newsletters-supsystic',
						'options' => array(
							'position' => array(
								'edge' => 'bottom',
								'align' => 'top',
							),
						),
						'show' => 'not_plugin',
					),
				),
			),
			'create_first' => array(
				'points' => array(
					'create_bar_btn' => array(
						'target' => '.supsystic-content .supsystic-navigation .supsystic-tab-newsletters_add_new',
						'options' => array(
							'position' => array(
								'edge' => 'left',
								'align' => 'right',
							),
						),
						'show' => array('tab_newsletters', 'tab_settings', 'tab_overview'),
					),
					'enter_title' => array(
						'target' => '#nbsCreateFormForm input[type=text]',
						'options' => array(
							'position' => array(
								'edge' => 'top',
								'align' => 'bottom',
							),
						),
						'show' => 'tab_newsletters_add_new',
					),
					'select_tpl' => array(
						'target' => '.newsletters-list',
						'options' => array(
							'position' => array(
								'edge' => 'bottom',
								'align' => 'top',
							),
						),
						'show' => 'tab_newsletters_add_new',
					),
					'save_first_newsletters' => array(
						'target' => '#nbsCreateFormForm .button-primary',
						'options' => array(
							'position' => array(
								'edge' => 'left',
								'align' => 'right',
							),
						),
						'show' => 'tab_newsletters_add_new',
					),
				),
			),
			'first_edit' => array(
				'points' => array(
					'newsletters_main_opts' => array(
						'target' => '#nbsFormEditForm',
						'options' => array(
							'position' => array(
								'edge' => 'right',
								'align' => 'left',
							),
							'pointerWidth' => 200,
						),
						'show' => 'tab_newsletters_edit',
					),
					'newsletters_design_opts' => array(
						'target' => '#nbsFormEditForm',
						'options' => array(
							'position' => array(
								'edge' => 'right',
								'align' => 'top',
							),
							'pointerWidth' => 200,
						),
						'show' => 'tab_newsletters_edit',
						'sub_tab' => '#nbsFormTpl',
					),
					'newsletters_subscribe_opts' => array(
						'target' => '#nbsFormEditForm',
						'options' => array(
							'position' => array(
								'edge' => 'right',
								'align' => 'top',
							),
							'pointerWidth' => 200,
						),
						'show' => 'tab_newsletters_edit',
						'sub_tab' => '#nbsFormSubscribe',
					),
					'newsletters_statistics_opts' => array(
						'target' => '#nbsFormEditForm',
						'options' => array(
							'position' => array(
								'edge' => 'right',
								'align' => 'left',
							),
							'pointerWidth' => 200,
						),
						'show' => 'tab_newsletters_edit',
						'sub_tab' => '#nbsFormStatistics',
					),
					'newsletters_code_opts' => array(
						'target' => '#nbsFormEditForm',
						'options' => array(
							'position' => array(
								'edge' => 'right',
								'align' => 'left',
							),
							'pointerWidth' => 200,
						),
						'show' => 'tab_newsletters_edit',
						'sub_tab' => '#nbsFormEditors',
					),
					'final' => array(
						'target' => '#nbsFormMainControllsShell .nbsFormSaveBtn',
						'options' => array(
							'position' => array(
								'edge' => 'top',
								'align' => 'bottom',
							),
							'pointerWidth' => 500,
						),
						'show' => 'tab_newsletters_edit',
					),
				),
			),
		);
		$isAdminPage = frameNbs::_()->isAdminPlugOptsPage();
		$activeTab = frameNbs::_()->getModule('options')->getActiveTab();
		foreach($tourData['tour'] as $stepId => $step) {
			foreach($step['points'] as $pointId => $point) {
				$pointKey = $stepId. '-'. $pointId;
				if(isset($hst['passed'][ $pointKey ]) && $hst['passed'][ $pointKey ]) {
					unset($tourData['tour'][ $stepId ]['points'][ $pointId ]);
					continue;
				}
				$show = isset($point['show']) ? $point['show'] : 'plugin';
				if(!is_array($show))
					$show = array( $show );
				if((in_array('plugin', $show) && !$isAdminPage) || (in_array('not_plugin', $show) && $isAdminPage)) {
					unset($tourData['tour'][ $stepId ]['points'][ $pointId ]);
					continue;
				}
				$showForTabs = false;
				$hideForTabs = false;
				foreach($show as $s) {
					if(strpos($s, 'tab_') === 0) {
						$showForTabs = true;
					}
					if(strpos($s, 'tab_not_') === 0) {
						$showForTabs = true;
					}
				}
				if($showForTabs && (!in_array('tab_'. $activeTab, $show) || !$isAdminPage)) {
					unset($tourData['tour'][ $stepId ]['points'][ $pointId ]);
					continue;
				}
				if($hideForTabs && (in_array('tab_not_'. $activeTab, $show) || !$isAdminPage)) {
					unset($tourData['tour'][ $stepId ]['points'][ $pointId ]);
					continue;
				}
				switch($pointKey) {
					case 'create_first-create_bar_btn':
						// Pointer for Create new POpUp we can show only if there are no created Forms
						$createdFormsNum = frameNbs::_()->getModule('newsletters')->getModel()->addWhere('original_id != 0')->getCount();
						if(!empty($createdFormsNum)) {
							unset($tourData['tour'][ $stepId ]['points'][ $pointId ]);
							continue 2;
						}
						break;
				}
			}
		}
		foreach($tourData['tour'] as $stepId => $step) {
			if(!isset($step['points']) || empty($step['points'])) {
				unset($tourData['tour'][ $stepId ]);
			}
		}
		if(empty($tourData['tour']))
			return;
		$tourData['html'] = $this->getView()->getTourHtml();
		frameNbs::_()->getModule('templates')->loadCoreJs();
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'wp-pointer' );
		frameNbs::_()->addScript(NBS_CODE. 'admin.tour', $this->getModPath(). 'js/admin.tour.js');
		frameNbs::_()->addJSVar(NBS_CODE. 'admin.tour', 'nbsAdminTourData', $tourData);
	}
	public function showFeaturedPluginsPage() {
		return $this->getView()->showFeaturedPluginsPage();
	}
	public function getDiscountMsg() {
		if($this->isPro()
			&& frameNbs::_()->getModule('options')->getActiveTab() == 'license'
			&& frameNbs::_()->getModule('license')
			&& frameNbs::_()->getModule('license')->getModel()->isActive()
		) {
			$proPluginsList = array(
				'ultimate-maps-by-supsystic-pro', 'newsletters-by-supsystic-pro', 'contact-form-by-supsystic-pro', 'live-chat-pro',
				'digital-publications-supsystic-pro', 'coming-soon-supsystic-pro', 'price-table-supsystic-pro', 'tables-generator-pro',
				'social-share-pro', 'popup-by-supsystic-pro', 'supsystic_slider_pro', 'supsystic-gallery-pro', 'google-maps-easy-pro',
				'backup-supsystic-pro',
			);
			$activePluginsList = get_option('active_plugins', array());
			$activeProPluginsCount = 0;
			foreach($activePluginsList as $actPl) {
				foreach($proPluginsList as $proPl) {
					if(strpos($actPl, $proPl) !== false) {
						$activeProPluginsCount++;
					}
				}
			}
			if($activeProPluginsCount === 1) {
				$buyLink = $this->getDiscountBuyUrl();
				$this->getView()->getDiscountMsg($buyLink);
			}
		}
	}
	public function getDiscountBuyUrl() {
		$license = frameNbs::_()->getModule('license')->getModel()->getCredentials();
		$license['key'] = md5($license['key']);
		$license = urlencode(base64_encode(implode('|', $license)));
		$plugin_code = 'newsletters_by_supsystic_pro';
		return 'http://supsystic.com/?mod=manager&pl=lms&action=applyDiscountBuyUrl&plugin_code='. $plugin_code. '&lic='. $license;
	}
}