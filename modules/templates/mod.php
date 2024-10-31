<?php
class templatesNbs extends moduleNbs {
	private $_cdnUrl = '';

	public function __construct($d) {
		parent::__construct($d);
		$this->getCdnUrl();	// Init CDN URL
	}
	public function getCdnUrl() {
		if(empty($this->_cdnUrl)) {
			if((int) frameNbs::_()->getModule('options')->get('use_local_cdn')) {
				$uploadsDir = wp_upload_dir( null, false );
				$this->_cdnUrl = $uploadsDir['baseurl']. '/'. NBS_CODE. '/';
				if(uriNbs::isHttps()) {
					$this->_cdnUrl = str_replace('http://', 'https://', $this->_cdnUrl);
				}
				dispatcherNbs::addFilter('externalCdnUrl', array($this, 'modifyExternalToLocalCdn'));
			} else {
				$this->_cdnUrl = /*(uriNbs::isHttps() ? 'https' : 'http'). */'https://supsystic-42d7.kxcdn.com/';
			}
		}
		return $this->_cdnUrl;
	}
	public function modifyExternalToLocalCdn( $url ) {
		$url = str_replace(
			array('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css'),
			array($this->_cdnUrl. 'lib/font-awesome'),
			$url);
		return $url;
	}
    public function init() {
        if (is_admin()) {
			if($isAdminPlugOptsPage = frameNbs::_()->isAdminPlugOptsPage()) {
				$this->loadCoreJs();
				$this->loadAdminCoreJs();
				$this->loadCoreCss();
				$this->loadAdminCoreCss();
				$this->loadChosenSelects();
				frameNbs::_()->addScript('adminOptionsNbs', NBS_JS_PATH. 'admin.options.js', array(), false, true);
				add_action('admin_enqueue_scripts', array($this, 'loadMediaScripts'));
				add_action('init', array($this, 'connectAdditionalAdminAssets'));
			}
			// Some common styles - that need to be on all admin pages - be careful with them
			frameNbs::_()->addStyle('supsystic-for-all-admin-'. NBS_CODE, NBS_CSS_PATH. 'supsystic-for-all-admin.css');
		}
        parent::init();
    }
	public function connectAdditionalAdminAssets() {
		if(is_rtl()) {
			frameNbs::_()->addStyle('styleNbs-rtl', NBS_CSS_PATH. 'style-rtl.css');
		}
	}
	public function loadMediaScripts() {
		if(function_exists('wp_enqueue_media')) {
			wp_enqueue_media();
		}
	}
	public function loadAdminCoreJs() {
		frameNbs::_()->addScript('jquery-ui-dialog');
		frameNbs::_()->addScript('jquery-ui-slider');
		frameNbs::_()->addScript('wp-color-picker');
		frameNbs::_()->addScript('icheck', NBS_JS_PATH. 'icheck.min.js');
		$this->loadTooltipster();
	}
	public function loadCoreJs() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('jquery');

			frameNbs::_()->addScript('commonNbs', NBS_JS_PATH. 'common.js');
			frameNbs::_()->addScript('coreNbs', NBS_JS_PATH. 'core.js');

			$ajaxurl = admin_url('admin-ajax.php');
			$jsData = array(
				'siteUrl'					=> NBS_SITE_URL,
				'imgPath'					=> NBS_IMG_PATH,
				'cssPath'					=> NBS_CSS_PATH,
				'loader'					=> NBS_LOADER_IMG,
				'close'						=> NBS_IMG_PATH. 'cross.gif',
				'ajaxurl'					=> $ajaxurl,
				'options'					=> frameNbs::_()->getModule('options')->getAllowedPublicOptions(),
				'NBS_CODE'					=> NBS_CODE,
				//'ball_loader'				=> NBS_IMG_PATH. 'ajax-loader-ball.gif',
				//'ok_icon'					=> NBS_IMG_PATH. 'ok-icon.png',
				'jsPath'					=> NBS_JS_PATH,
			);
			if(is_admin()) {
				$jsData['isPro'] = frameNbs::_()->getModule('supsystic_promo')->isPro();
			}
			$jsData = dispatcherNbs::applyFilters('jsInitVariables', $jsData);
			frameNbs::_()->addJSVar('coreNbs', 'NBS_DATA', $jsData);
			$loaded = true;
		}
	}

	public function loadTooltipster() {
		frameNbs::_()->addScript('tooltipster', $this->_cdnUrl. 'lib/tooltipster/jquery.tooltipster.min.js');
		frameNbs::_()->addStyle('tooltipster', $this->_cdnUrl. 'lib/tooltipster/tooltipster.css');
	}
	public function loadSlimscroll($overview = false) {
		// Local copy is modified specially for Octo builder
        if ($overview) {
            frameNbs::_()->addScript('jquery.slimscroll', $this->_cdnUrl. 'js/jquery.slimscroll.js');
        } else {
            frameNbs::_()->addScript('jquery.slimscroll', NBS_JS_PATH . 'jquery.slimscroll.js');
        }
	}
	public function loadCodemirror() {
		frameNbs::_()->addStyle('nbsCodemirror', $this->_cdnUrl. 'lib/codemirror/codemirror.css');
		frameNbs::_()->addStyle('codemirror-addon-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/show-hint.css');
		frameNbs::_()->addScript('nbsCodemirror', $this->_cdnUrl. 'lib/codemirror/codemirror.js');
		frameNbs::_()->addScript('codemirror-addon-show-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/show-hint.js');
		frameNbs::_()->addScript('codemirror-addon-xml-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/xml-hint.js');
		frameNbs::_()->addScript('codemirror-addon-html-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/html-hint.js');
		frameNbs::_()->addScript('codemirror-mode-xml', $this->_cdnUrl. 'lib/codemirror/mode/xml/xml.js');
		frameNbs::_()->addScript('codemirror-mode-javascript', $this->_cdnUrl. 'lib/codemirror/mode/javascript/javascript.js');
		frameNbs::_()->addScript('codemirror-mode-css', $this->_cdnUrl. 'lib/codemirror/mode/css/css.js');
		frameNbs::_()->addScript('codemirror-mode-htmlmixed', $this->_cdnUrl. 'lib/codemirror/mode/htmlmixed/htmlmixed.js');
	}
	public function loadCoreCss() {
		$styles = array(
			'styleNbs'			=> array('path' => NBS_CSS_PATH. 'style.css', 'for' => 'admin'),
		);
		foreach($styles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				frameNbs::_()->addStyle($s, $sInfo['path']);
			} else {
				frameNbs::_()->addStyle($s);
			}
		}
	}
	public function loadAdminCoreCss() {
		$styles = array(
			'supsystic-uiNbs'	=> array('path' => NBS_CSS_PATH. 'supsystic-ui.css', 'for' => 'admin'),
			'dashicons'			=> array('for' => 'admin'),
			'bootstrap-alerts'	=> array('path' => NBS_CSS_PATH. 'bootstrap-alerts.css', 'for' => 'admin'),
			'icheck'			=> array('path' => NBS_CSS_PATH. 'jquery.icheck.css', 'for' => 'admin'),
			'wp-color-picker'	=> array('for' => 'admin'),
		);
		foreach($styles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				frameNbs::_()->addStyle($s, $sInfo['path']);
			} else {
				frameNbs::_()->addStyle($s);
			}
		}
		$this->loadFontAwesome();
	}
	public function loadJqueryUi() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addStyle('jquery-ui', NBS_CSS_PATH. 'jquery-ui.min.css');
			frameNbs::_()->addStyle('jquery-ui.structure', NBS_CSS_PATH. 'jquery-ui.structure.min.css');
			frameNbs::_()->addStyle('jquery-ui.theme', NBS_CSS_PATH. 'jquery-ui.theme.min.css');
			frameNbs::_()->addStyle('jquery-slider', NBS_CSS_PATH. 'jquery-slider.css');
			$loaded = true;
		}
	}
	public function loadJqGrid() {
		static $loaded = false;
		if(!$loaded) {
			$this->loadJqueryUi();
			frameNbs::_()->addScript('jq-grid', $this->_cdnUrl. 'lib/jqgrid/jquery.jqGrid.min.js');
			frameNbs::_()->addStyle('jq-grid', $this->_cdnUrl. 'lib/jqgrid/ui.jqgrid.css');
			$langToLoad = utilsNbs::getLangCode2Letter();
			$availableLocales = array('ar','bg','bg1251','cat','cn','cs','da','de','dk','el','en','es','fa','fi','fr','gl','he','hr','hr1250','hu','id','is','it','ja','kr','lt','mne','nl','no','pl','pt','pt','ro','ru','sk','sr','sr','sv','th','tr','tw','ua','vi');
			if(!in_array($langToLoad, $availableLocales)) {
				$langToLoad = 'en';
			}
			frameNbs::_()->addScript('jq-grid-lang', $this->_cdnUrl. 'lib/jqgrid/i18n/grid.locale-'. $langToLoad. '.js');
			$loaded = true;
		}
	}
	public function loadFontAwesome() {
		frameNbs::_()->addStyle('font-awesomeNbs', dispatcherNbs::applyFilters('externalCdnUrl', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'));
	}
	public function loadChosenSelects() {
		frameNbs::_()->addStyle('jquery.chosen', $this->_cdnUrl. 'lib/chosen/chosen.min.css');
		frameNbs::_()->addScript('jquery.chosen', $this->_cdnUrl. 'lib/chosen/chosen.jquery.min.js');
	}
	public function loadDatePicker() {
		frameNbs::_()->addScript('jquery-ui-datepicker');
	}
	public function loadJqplot() {
		static $loaded = false;
		if(!$loaded) {
			$jqplotDir = $this->_cdnUrl. 'lib/jqplot/';

			frameNbs::_()->addStyle('jquery.jqplot', $jqplotDir. 'jquery.jqplot.min.css');

			frameNbs::_()->addScript('jplot', $jqplotDir. 'jquery.jqplot.min.js');
			frameNbs::_()->addScript('jqplot.canvasAxisLabelRenderer', $jqplotDir. 'jqplot.canvasAxisLabelRenderer.min.js');
			frameNbs::_()->addScript('jqplot.canvasTextRenderer', $jqplotDir. 'jqplot.canvasTextRenderer.min.js');
			frameNbs::_()->addScript('jqplot.dateAxisRenderer', $jqplotDir. 'jqplot.dateAxisRenderer.min.js');
			frameNbs::_()->addScript('jqplot.canvasAxisTickRenderer', $jqplotDir. 'jqplot.canvasAxisTickRenderer.min.js');
			frameNbs::_()->addScript('jqplot.highlighter', $jqplotDir. 'jqplot.highlighter.min.js');
			frameNbs::_()->addScript('jqplot.cursor', $jqplotDir. 'jqplot.cursor.min.js');
			frameNbs::_()->addScript('jqplot.barRenderer', $jqplotDir. 'jqplot.barRenderer.min.js');
			frameNbs::_()->addScript('jqplot.categoryAxisRenderer', $jqplotDir. 'jqplot.categoryAxisRenderer.min.js');
			frameNbs::_()->addScript('jqplot.pointLabels', $jqplotDir. 'jqplot.pointLabels.min.js');
			frameNbs::_()->addScript('jqplot.pieRenderer', $jqplotDir. 'jqplot.pieRenderer.min.js');
			$loaded = true;
		}
	}
	public function loadSortable() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('jquery-ui-core');
			frameNbs::_()->addScript('jquery-ui-widget');
			frameNbs::_()->addScript('jquery-ui-mouse');

			frameNbs::_()->addScript('jquery-ui-draggable');
			frameNbs::_()->addScript('jquery-ui-sortable');
			$loaded = true;
		}
	}
	public function loadMagicAnims() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addStyle('magic.anim', $this->_cdnUrl. 'css/magic.min.css');
			$loaded = true;
		}
	}
	public function loadCssAnims() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addStyle('animate.styles', NBS_CSS_PATH. 'animate.min.css');
			$loaded = true;
		}
	}
	public function loadBootstrapPartial() {
		static $loaded = false;
		if(!$loaded) {
			$this->loadBootstrapPartialOnlyCss();
			frameNbs::_()->addScript('bootstrap', NBS_JS_PATH. 'bootstrap.min.js');
			frameNbs::_()->addStyle('jasny-bootstrap', NBS_CSS_PATH. 'jasny-bootstrap.min.css');
			frameNbs::_()->addScript('jasny-bootstrap', NBS_JS_PATH. 'jasny-bootstrap.min.js');
			$loaded = true;
		}
	}
	public function loadBootstrapPartialOnlyCss() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addStyle('bootstrap.partial', frameNbs::_()->getModule('newsletters')->getAssetsUrl(). 'css/bootstrap.partial.min.css');
			$loaded = true;
		}
	}
	public function connectWpMceEditor() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('tiny_mce');
			$loaded = true;
		}
	}
	public function loadSerializeJson() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('jquery.serializejson', NBS_JS_PATH. 'jquery.serializejson.min.js');
			$loaded = true;
		}
	}
	public function loadTimePicker() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addStyle('jquery.timepicker', NBS_CSS_PATH. 'jquery.timepicker.css');
			frameNbs::_()->addScript('jquery.timepicker', NBS_JS_PATH. 'jquery.timepicker.min.js');
			$loaded = true;
		}
	}
	public function loadDateTimePicker() {
		frameNbs::_()->addScript('jquery-datetimepicker', NBS_JS_PATH . 'datetimepicker/jquery.datetimepicker.min.js');
		frameNbs::_()->addStyle('jquery-datetimepicker', NBS_JS_PATH . 'datetimepicker/jquery.datetimepicker.css');
	}
	public function loadBootstrap() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addStyle('bootstrap', frameNbs::_()->getModule('octo')->getAssetsUrl(). 'css/bootstrap.min.css');
			frameNbs::_()->addStyle('bootstrap-theme', frameNbs::_()->getModule('octo')->getAssetsUrl(). 'css/bootstrap-theme.min.css');
			frameNbs::_()->addScript('bootstrap', NBS_JS_PATH. 'bootstrap.min.js');

			frameNbs::_()->addStyle('jasny-bootstrap', NBS_CSS_PATH. 'jasny-bootstrap.min.css');
			frameNbs::_()->addScript('jasny-bootstrap', NBS_JS_PATH. 'jasny-bootstrap.min.js');
			$loaded = true;
		}
	}
	public function loadTinyMce() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('nbs.tinymce', NBS_JS_PATH. 'tinymce/tinymce.min.js');
			frameNbs::_()->addScript('nbs.jquery.tinymce', NBS_JS_PATH. 'tinymce/jquery.tinymce.min.js');
			$loaded = true;
		}
	}
	public function loadCustomColorpicker() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('jquery.colorpicker.spectrum', NBS_JS_PATH. 'jquery.colorpicker/spectrum.js');
			frameNbs::_()->addStyle('jquery.colorpicker.spectrum', NBS_JS_PATH. 'jquery.colorpicker/spectrum.css');
			$loaded = true;
		}
	}
	public function loadCustomBootstrapColorpicker() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('oct.colors.script', NBS_JS_PATH. 'colorPicker/color.all.min.js');
			frameNbs::_()->addStyle('oct.colors.style', NBS_JS_PATH. 'colorPicker/color.css');

			frameNbs::_()->addScript('jquery.bootstrap.colorpicker.tinycolor', NBS_JS_PATH. 'jquery.bootstrap.colorpicker/tinycolor.js');
			frameNbs::_()->addScript('jquery.bootstrap.colorpicker', NBS_JS_PATH. 'jquery.bootstrap.colorpicker/jquery.colorpickersliders.js');
			frameNbs::_()->addStyle('jquery.bootstrap.colorpicker', NBS_JS_PATH. 'jquery.bootstrap.colorpicker/jquery.colorpickersliders.css');
			$loaded = true;
		}
	}
	public function loadContextMenu() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('jquery-ui-position');
			frameNbs::_()->addScript('jquery.contextMenu', NBS_JS_PATH. 'jquery.context-menu/jquery.contextMenu.js');
			frameNbs::_()->addStyle('jquery.contextMenu', NBS_JS_PATH. 'jquery.context-menu/jquery.contextMenu.css');
			$loaded = true;
		}
	}
	/**
	 * Load JS lightbox plugin, for now - this is prettyphoto
	 */
	public function loadLightbox() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addScript('prettyphoto', NBS_JS_PATH. 'prettyphoto/js/jquery.prettyPhoto.js');
			frameNbs::_()->addStyle('prettyphoto', NBS_JS_PATH. 'prettyphoto/css/prettyPhoto.css');
			$loaded = true;
		}
	}
	public function loadTooltipstered() {
		frameNbs::_()->addScript('tooltipster', $this->_cdnUrl. 'lib/tooltipster/jquery.tooltipster.min.js');
		frameNbs::_()->addStyle('tooltipster', $this->_cdnUrl. 'lib/tooltipster/tooltipster.css');
		frameNbs::_()->addScript('tooltipsteredNbs', NBS_JS_PATH. 'tooltipstered.js', array('jquery'));
	}
	public function loadBootstrapSimple() {
		static $loaded = false;
		if(!$loaded) {
			frameNbs::_()->addStyle('bootstrap-nbs', NBS_CSS_PATH. 'bootstrap.min.css');
			$loaded = true;
		}
	}
	public function loadGoogleCharts() {
		frameNbs::_()->addScript('google.charts', 'https://www.gstatic.com/charts/loader.js');
	}
	public function loadGoogleFont( $font ) {
		static $loaded = array();
		if(!isset($loaded[ $font ])) {
			frameNbs::_()->addStyle('google.font.'. str_replace(array(' '), '-', $font), 'https://fonts.googleapis.com/css?family='. urlencode($font));
			$loaded[ $font ] = 1;
		}
	}
}
