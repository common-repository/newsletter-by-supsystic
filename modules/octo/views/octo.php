<?php
class octoViewNbs extends viewNbs {
	protected $_twig;
	private $_isSimple = false;
	public function getTabContent() {
		frameNbs::_()->getModule('templates')->loadJqGrid();
		frameNbs::_()->addStyle('admin.octo', $this->getModule()->getModPath(). 'css/admin.octo.css');
		frameNbs::_()->addScript('admin.octo', $this->getModule()->getModPath(). 'js/admin.octo.js');
		frameNbs::_()->addScript('admin.octo.list', $this->getModule()->getModPath(). 'js/admin.octo.list.js');
		frameNbs::_()->addJSVar('admin.octo.list', 'octTblDataUrl', uriNbs::mod('octo', 'getListForTbl', array('reqType' => 'ajax')));

		//$this->assign('addNewLink', frameNbs::_()->getModule('options')->getTabUrl('NBS_add_new'));
		return parent::getContent('octoAdmin');
	}
	public function showMainMetaBox($post) {
		frameNbs::_()->getModule('templates')->loadCoreJs();
		frameNbs::_()->getModule('templates')->loadAdminCoreJs();
		frameNbs::_()->addScript('admin.octo.post', $this->getModule()->getModPath(). 'js/admin.octo.post.js');
		frameNbs::_()->addStyle('admin.octo.post', $this->getModule()->getModPath(). 'css/admin.octo.post.css');
		frameNbs::_()->addStyle('frontend.octo.editor.octo-icons', $this->getModule()->getAssetsUrl(). 'css/octo-icons.css');
		$this->assign('isPostConverted', $this->getModel()->isPostConverted( $post->ID ));
		$this->assign('post', $post);
		$this->assign('usedBlocksNumber', $this->getModel()->getUsedBlocksNumForPost( $post->ID ));
		parent::display('octoMainMetaBox');
	}
	public function renderForPost($oid, $params = array()) {
		//frameNbs::_()->setStylesInitialized(false);
		//frameNbs::_()->setScriptsInitialized(false);
		$this->_isSimple = isset($params['simple']) ? $params['simple'] : false;
		$isEditMode = isset($params['isEditMode']) ? $params['isEditMode'] : false;
		$isPreviewMode = isset($params['isPreviewMode']) ? $params['isPreviewMode'] : false;

		add_action('wp_enqueue_scripts', array($this, 'filterScripts'));
		add_action('wp_print_styles', array($this, 'filterStyles'));

		$this->getModel()->clearDynPostIds( $oid );	// Clear prev. generated dyn post IDs
		$octo = $this->getModel()->getFullById($oid);
		if($isEditMode) {
			$this->loadWpAdminAssets();
		}
		frameNbs::_()->getModule('templates')->loadCoreJs();

		if(!$this->_isSimple) {
			frameNbs::_()->getModule('templates')->loadBootstrap();
			frameNbs::_()->getModule('templates')->loadCustomBootstrapColorpicker();
		}
		$this->assignRef('octo', $octo);
		$this->connectFrontendAssets( $octo, $isEditMode, $isPreviewMode );
		if($isEditMode) {
			$originalBlocksByMissions = $this->getModel('octo_blocks')->getOriginalBlocksByMissions();
			$this->assign('originalBlocksByMissions', $originalBlocksByMissions);
			$this->connectEditorAssets( $octo, $isPreviewMode );
			$this->assign('allPagesUrl', frameNbs::_()->getModule('options')->getTabUrl('settings'));
			$this->assign('noImgUrl', $this->getModule()->getAssetsUrl(). 'img/no-photo.png');
		}
		$this->assign('zoom', isset($params['zoom']) ? $params['zoom'] : false);
		$this->assign('zoomOrigin', isset($params['zoomOrigin']) ? $params['zoomOrigin'] : false);
		$this->assign('isSimple', $this->_isSimple);

		$this->_prepareOctoForRender( $octo, $isEditMode );

		$this->assign('isEditMode', $isEditMode);

		$this->assign('stylesScriptsHtml', $this->generateStylesScriptsHtml());
		// Render this part - at final step
		$this->assign('commonFooter', $this->getCommonFooter());
		if($isEditMode) {
			$this->assign('editorFooter', $this->getEditorFooter());
		} else {
			$this->assign('footer', $this->getFooter());
		}
		if(isset($params['returnContent']) && $params['returnContent']) {
			return parent::getContent('octoRenderForPost');
		}
		parent::display('octoRenderForPost');
	}
	public function getEditorFooter() {
		$post_types = get_post_types('', 'objects');
		$postTypesForSelect = array();
		foreach($post_types as $key => $value) {
			if(!in_array($key, array('attachment', 'revision', 'nav_menu_item'))) {
				$postTypesForSelect[$key] = $value->labels->name;
			}
		}
		$this->assign('postTypesForSelect', $postTypesForSelect);
		$imgWidthUnits = array('px' => 'px', '%' => '%');
		$this->assign('imgWidthUnits', $imgWidthUnits);
		//$elementsForInsert = $this->getModule()->getElements();
		//$this->assign('elementsForInsert', $elementsForInsert);
		return parent::getContent('octoEditorFooter');
	}
	public function getFooter() {
		return parent::getContent('octoFooter');
	}
	// Footer parts that need to be in frontend and in editor too
	public function getCommonFooter() {
		$this->assign('isSimple', $this->_isSimple);
		return parent::getContent('octoCommonFooter');
	}
	private function _prepareOctoForRender(&$octo, $isEditMode = false) {
		if(!empty($octo['blocks'])) {
			foreach($octo['blocks'] as $i => $block) {
				$octo['blocks'][ $i ]['rendered_html'] = $this->renderBlock( $octo['blocks'][ $i ], $isEditMode, $octo['params'] );
			}
		}
	}
	public function renderBlock($block = array(), $isEditMode = false, $canvasParams = array()) {
		$this->assign('block', $block);
		$this->assign('isEditMode', $isEditMode);
		$content = parent::getContent('octoRenderBlock');
		if(!$isEditMode && $this->_isSimple) {
			if(isset($block['params'], $block['params']['align']) && !empty($block['params']['align']['val'])) {
				$content = str_replace(array('<td'), array('<td align="'. $block['params']['align']['val']. '"'), $content);
			}
		}
		$this->_initTwig();
		return $this->_twig->render($content, array('block' => $block, 'canvasParams' => $canvasParams));
	}
	public function connectFrontendAssets( $octo = array(), $isEditMode = false, $isPreviewMode = false ) {
		frameNbs::_()->getModule('templates')->loadFontAwesome();
		if($this->_isSimple) {
			frameNbs::_()->addStyle('frontend.octo', $this->getModule()->getModPath(). 'css/frontend.octo.simple.css');
		} else {
			frameNbs::_()->addStyle('animate', $this->getModule()->getAssetsUrl(). 'css/animate.css');
			frameNbs::_()->addStyle('frontend.octo', $this->getModule()->getModPath(). 'css/frontend.octo.css');
			frameNbs::_()->addStyle('slider.bx', $this->getModule()->getModPath(). 'assets/sliders/bx/jquery.bxslider.css');
			frameNbs::_()->addScript('slider.bx', $this->getModule()->getModPath(). 'assets/sliders/bx/jquery.bxslider.min.js');
			frameNbs::_()->getModule('templates')->loadGoogleFont('Roboto');	// Load font for builder
		}
		frameNbs::_()->addScript('frontend.octo.canvas', $this->getModule()->getModPath(). 'js/frontend.octo.canvas.js');
		frameNbs::_()->addScript('frontend.octo.editor.blocks_fabric.base', $this->getModule()->getModPath(). 'js/frontend.octo.editor.blocks_fabric.base.js');
		frameNbs::_()->addScript('frontend.octo.editor.blocks.base', $this->getModule()->getModPath(). 'js/frontend.octo.editor.blocks.base.js');
		frameNbs::_()->addScript('frontend.octo.editor.elements.base', $this->getModule()->getModPath(). 'js/frontend.octo.editor.elements.base.js');

		frameNbs::_()->addScript('frontend.octo', $this->getModule()->getModPath(). 'js/frontend.octo.js');
		frameNbs::_()->addJSVar('frontend.octo', 'nbsBuildConst', array(
			'standardFonts' => utilsNbs::getStandardFontsList(),
			'defCanvasWidth' => NBS_DEF_WIDTH,
			'defCanvasWidthUnits' => NBS_DEF_WIDTH_UNITS,
		));

		$octo['time'] = getdate(current_time('timestamp'));
		$octo['isPreviewMode'] = $isPreviewMode;

		frameNbs::_()->addJSVar('frontend.octo', 'nbsOcto', $octo);
		if(!$this->_isSimple) {
			frameNbs::_()->getModule('templates')->loadLightbox();
		}
	}
	public function connectEditorAssets( $octo = array() ) {
		$this->assign('adminEmail', get_bloginfo('admin_email'));
		$this->connectEditorJs( $octo );
		$this->connectEditorCss( $octo );
	}
	public function connectEditorJs( $octo = array() ) {
		global $wpdb;

		frameNbs::_()->addScript('jquery-ui-core');
		frameNbs::_()->addScript('jquery-ui-widget');
		frameNbs::_()->addScript('jquery-ui-mouse');

		frameNbs::_()->addScript('jquery-ui-draggable');
		frameNbs::_()->addScript('jquery-ui-sortable');
		//frameNbs::_()->addScript('jquery-ui-dialog');
		frameNbs::_()->addScript('jquery-ui-slider');
		frameNbs::_()->addScript('jquery-ui-resizable');

		frameNbs::_()->getModule('templates')->loadMediaScripts();
		frameNbs::_()->getModule('templates')->loadCustomBootstrapColorpicker();
		frameNbs::_()->getModule('templates')->loadTinyMce();
		frameNbs::_()->getModule('templates')->loadContextMenu();
		//frameNbs::_()->getModule('templates')->loadCustomColorpicker();

		frameNbs::_()->addScript('twig', NBS_JS_PATH. 'twig.min.js');
		frameNbs::_()->addScript('icheck', NBS_JS_PATH. 'icheck.min.js');
		frameNbs::_()->getModule('templates')->loadSlimscroll();
		frameNbs::_()->addScript('frontend.octo.editor.menus', $this->getModule()->getModPath(). 'js/frontend.octo.editor.menus.js');
		frameNbs::_()->addScript('wp.tabs', NBS_JS_PATH. 'wp.tabs.js');

		frameNbs::_()->addScript('frontend.octo.editor.tbl-resizer', $this->getModule()->getModPath(). 'js/frontend.octo.editor.tbl-resizer.js');
		frameNbs::_()->addScript('frontend.octo.editor.maintoolbar', $this->getModule()->getModPath(). 'js/frontend.octo.editor.maintoolbar.js');
		frameNbs::_()->addScript('frontend.octo.editor.utils', $this->getModule()->getModPath(). 'js/frontend.octo.editor.utils.js');
		frameNbs::_()->addScript('frontend.octo.editor.blocks_fabric', $this->getModule()->getModPath(). 'js/frontend.octo.editor.blocks_fabric.js');
		frameNbs::_()->addScript('frontend.octo.editor.elements', $this->getModule()->getModPath(). 'js/frontend.octo.editor.elements.js');
		frameNbs::_()->addScript('frontend.octo.editor.elements.menu', $this->getModule()->getModPath(). 'js/frontend.octo.editor.elements.menu.js');
		frameNbs::_()->addScript('frontend.octo.editor.blocks', $this->getModule()->getModPath(). 'js/frontend.octo.editor.blocks.js');
		frameNbs::_()->addScript('frontend.octo.editor', $this->getModule()->getModPath(). 'js/frontend.octo.editor.js');
		//frameNbs::_()->addJSVar('frontend.octo.editor', 'octOcto', $octo);
		frameNbs::_()->getModule('templates')->loadChosenSelects();
		frameNbs::_()->getModule('templates')->loadJqueryUi();
		frameNbs::_()->getModule('templates')->loadTooltipstered();

		$nbsEditor = array();
		$nbsEditor['posts'] = array();

		// Don't use get_posts() here - it's too match data returned by this functiion for our case - let's save some memory for our users:)
		$postTypesForPostsList = array('page', 'post', 'product', 'blog');
		$allPosts = dbNbs::get("SELECT ID, post_title FROM $wpdb->posts WHERE post_type IN ('". implode("','", $postTypesForPostsList). "') AND post_status IN ('publish','draft') ORDER BY post_title");

		if ($allPosts) {
			foreach ($allPosts as $post) {
				$nbsEditor['posts'][] = array(
					'url' => get_permalink($post['ID']),
					'title' => $post['post_title'],
				);
			}
		}

		frameNbs::_()->addJSVar('frontend.octo.editor', 'nbsEditor', $nbsEditor);
	}
	public function connectEditorCss( $octo = array() ) {
		// We will use other instance of this lib here - to use prev. one in admin area
		frameNbs::_()->addStyle('octo.jquery.icheck', $this->getModule()->getModPath(). 'css/jquery.icheck.css');
		frameNbs::_()->addStyle('frontend.octo.editor', $this->getModule()->getModPath(). 'css/frontend.octo.editor.css');
		frameNbs::_()->addStyle('frontend.octo.editor.tinymce', $this->getModule()->getModPath(). 'css/frontend.octo.editor.tinymce.css');
		frameNbs::_()->addStyle('frontend.octo.editor.octo-icons', $this->getModule()->getAssetsUrl(). 'css/octo-icons.css');
		frameNbs::_()->addStyle('supsystic-uiNbs', NBS_CSS_PATH. 'supsystic-ui.css');
	}
	public function loadWpAdminAssets() {
		frameNbs::_()->addStyle('wp.common', get_admin_url(). 'css/common.css');
	}
	public function generateWpScriptsStyles() {
		global $wp_scripts, $wp_styles;
		if(!$wp_scripts && !$wp_styles) return '';
		$this->assign('wpScripts', $wp_scripts);
		$this->assign('wpStyles', $wp_styles);
		return parent::getContent('octoWpScripts');
	}
	public function filterScripts() {
		global $wp_scripts;

		if (! $wp_scripts) return;

		$scripts = array();

		foreach ($wp_scripts->registered as $script) {
			if (strpos($script->src, '/wp-content/themes') === false) {
				$scripts[] = $script;
			}
		}

		$wp_scripts->registered = $scripts;
	}
	public function filterStyles() {
		global $wp_styles;

		if (! $wp_styles) return;

		$styles = array();

		foreach ($wp_styles->registered as $style) {
			if (strpos($style->src, '/wp-content/themes') === false) {
				$styles[] = $style;
			}
		}

		$wp_styles->registered = $styles;
	}
	public function generateStylesScriptsHtml() {
		$res = array();
		if(version_compare(get_bloginfo('version'), '4.2.0', '<')) {
			global $wp_scripts;
			if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
				$wp_scripts = new WP_Scripts();
			}
		} else {
			$wp_scripts = wp_scripts();
		}
		$sufix = SCRIPT_DEBUG ? '' : '.min';

		$res[] = $this->generateWpScriptsStyles();
		$styles = frameNbs::_()->getStyles();
		if(!empty($styles)) {
			$usedHandles = array();
			$rel = 'stylesheet';
			$media = 'all';
			foreach($styles as $s) {
				if(!isset($usedHandles[ $s['handle'] ])) {
					$handle = $s['handle'];
					// TODO: add default wp src here - to search it by handles
					$rtl_href = isset($s['src']) ? $s['src'] : '';
					$res[] = "<link rel='$rel' id='$handle-rtl-css' href='$rtl_href' type='text/css' media='$media' />";
					$usedHandles[ $s['handle'] ] = 1;
				}
			}
		}
		$jsVars = frameNbs::_()->getJSVars();
		if(!empty($jsVars)) {
			$res[] = "<script type='text/javascript'>"; // CDATA and type='text/javascript' is not needed for HTML 5
			$res[] = "/* <![CDATA[ */";
			foreach($jsVars as $scriptH => $vars) {
				foreach($vars as $name => $value) {
					if($name == 'dataNoJson' && !is_array($value)) {
						$res[] = $value;
					} else {
						$res[] = "var $name = ". utilsNbs::jsonEncode($value). ";";
					}
				}
			}
			$res[] = "/* ]]> */";
			$res[] = "</script>";
		}
		return implode(NBS_EOL, $res);
	}
	protected function _initTwig() {
		if(!$this->_twig) {
			if(!class_exists('Twig_Autoloader')) {
				require_once(NBS_CLASSES_DIR. 'Twig'. DS. 'Autoloader.php');
			}
			Twig_Autoloader::register();
			$this->_twig = new Twig_Environment(new Twig_Loader_String(), array('debug' => 1));
			$this->_twig->addFunction(
				new Twig_SimpleFunction('adjBs'	/*adjustBrightness*/, array(
						$this,
						'adjustBrightness'
					)
				)
			);
			$this->_twig->addFunction(
				new Twig_SimpleFunction('hexToRgb', array(
						$this,
						'hexToRgb'
					)
				)
			);
			$this->_twig->addFunction(
				new Twig_SimpleFunction('dynContent', array(
						$this,
						'generateDynContent'
					)
				)
			);
		}
	}

	private function _isImageWidthInPixels($blockParams) {
		return !isset($blockParams['img_width_units']) || $blockParams['img_width_units']['val'] != '%';
	}

	private function _canDetermineImageWidth($blockParams, $fullWidth) {
		return isset($blockParams['img_width']) && $blockParams['img_width']['val']
			&& ($this->_isImageWidthInPixels($blockParams) || $fullWidth > 0 || isset($blockParams['full_width']));
	}

	/**
	 * @param array $block a block describing array
	 * @param int $fullWidth the width in pixels equivalent to 100% of image width, required to calculate real pixel image width to request from server
	 * @param array	$canvasParams array of canvas parameters
	 * @return string
	 *
	 * fullWidth requirements:
	 * only needed if you plan to use img_width_units == '%', i.e. you will point image width as percents.
	 * When you point percents during template render server any way should calculate real image pixel width
	 * to build URL for properly resized image.
	 * So this function should know how much pixels contains 100% of width. The way to calculate this is to use canvasParams variable
	 * available in template context. The twig template call to dynContent may be as follows:
	 * a) when you build two columns block with 40 px vertical delimiter:
	 *     	{% set fullWidth = (canvasParams.width - 40) / 2 %}
	 *      {{ dynContent(block, fullWidth, canvasParams)|raw }}
	 * b) when you build full width single column block:
	 *      {{ dynContent(block, canvasParams.width, canvasParams)|raw }}
	 * It is desirable to specify full_width parameter in block parameters equal to supposed value of fullWidth.
	 * It is used in block design context as there no canvas environment available and canvasParams.width
	 * not available respectively and in template design context as is renders dyn part of block separately
	 * from static part and fullWidth also not available. It`s fallback value.
	 */
	public function generateDynContent($block, $fullWidth = 0, $canvasParams = array() ) {
		$blockParams = $block['params'];
		$tpl = $blockParams['posts_tpl']['val'];
		$type = $blockParams['posts_type']['val'];
		$cnt = $blockParams['posts_cnt']['val'];
		if (empty($canvasParams) && isset($this->octo)) {
			$canvasParams = $this->octo['params'];
		}
		$getPostsParams = array(
			'posts_per_page' => $cnt,
			'post_type' => $type,
		);
		$oid = isset($block['oid']) ? $block['oid'] : false;
		if($oid) {
			$usedDynPostIds = $this->getModel()->getDynPostIds( $oid );
			if($usedDynPostIds) {
				$getPostsParams['exclude'] = $usedDynPostIds;
			}
		}
		$posts = get_posts( $getPostsParams );
		if($posts) {
			// 0 => head, 1 => part for post, 2 => footer
			$tplArr = explode('[posts_loop_start]', $tpl);
			$tplArr = array_merge(array($tplArr[ 0 ]), explode('[/posts_loop_start]', $tplArr[ 1 ]));
			$postsParts = array( $tplArr[ 0 ] );
			$themeHaveImg = strpos($tplArr[ 1 ], 'post_img_url') !== false;
			$addUsedDynPostIds = array();
			$realImgWidth = isset($blockParams['img_width']) ? $blockParams['img_width']['val'] : 0;
			foreach($posts as $post) {
				$postTpl = $tplArr[ 1 ];
				$postExcerpt = trim(apply_filters('the_excerpt', get_post_field('post_excerpt', $post->ID)));
				if(empty($postExcerpt)) {	// Let's insert there full post content when excerpt is empty
					$postExcerpt = $post->post_content;
					$postExcerpt = apply_filters('the_content', $postExcerpt);
					$postExcerpt = str_replace(']]>', ']]&gt;', $postExcerpt);
				}
				$replaceVars = array(
					'post_title' => $post->post_title,
					'post_excerpt' => $postExcerpt,
					'post_url' => get_permalink( $post ),
					'more_btn_txt' => isset($blockParams['more_btn_txt']) && $blockParams['more_btn_txt'] ? $blockParams['more_btn_txt']['val'] : __('Read More', NBS_LANG_CODE),
				);
				if($themeHaveImg) {
					$attachId = get_post_thumbnail_id($post->ID);
					$postImgUrl = null;
					if($attachId) {
						if($this->_canDetermineImageWidth($blockParams, $fullWidth)) {
							if ($this->_isImageWidthInPixels($blockParams)) {
								$realImgWidth = (int)$blockParams['img_width']['val'];
							} else {
								$fullWidth = $fullWidth > 0 ? (float)$fullWidth : (float)$blockParams['full_width']['val'];
								$realImgWidth = (int)(($fullWidth * (float)$blockParams['img_width']['val']) / 100.0);
							}
							$postImgUrl = $this->getModel('attach')->getAttachment($attachId, $realImgWidth);
						} else {
							$postImgUrl = wp_get_attachment_url( $attachId );
						}
					}
					if($postImgUrl) {
						$replaceVars['post_img_url'] = $postImgUrl;
					} else {	// Post do not have image - removeimg block from theme
						$postTpl = preg_replace('/<\!--post_img_shell-->.+<\!--\/post_img_shell-->/si', '', $postTpl);
					}
				}
				$postsParts[] = utilsNbs::replaceVariables($postTpl, $replaceVars);
				$addUsedDynPostIds[] = $post->ID;
			}
			$this->getModel()->addDynPostIds( $oid, $addUsedDynPostIds );
			$postsParts[] = $tplArr[ 2 ];
			$this->_initTwig();
			$renderedContent = $this->_twig->render(implode('', $postsParts), array('blockParams' => $blockParams, 'realImgWidth' => $realImgWidth));
			if (!empty($canvasParams) && isset($canvasParams['font_family'])) {
				$renderedContent = utilsNbs::setElementsStyle($renderedContent, '//td', array('font-family' => $canvasParams['font_family']));
			}
			return $renderedContent;
		}
		return '';
	}
	public function hexToRgb($string, $alpha = false) {
		if(strpos($string, 'rgb') !== false)
			return $string;
		$rgb = utilsNbs::hexToRgb( $string );
		$rgbStr = 'rgb';
		if($alpha !== false) {
			$rgb[] = $alpha;
			$rgbStr .= 'a';
		}
		return $rgbStr. '('. implode(',', $rgb). ')';
	}
	public function adjustBrightness($hex, $steps) {
		$isRgb = (strpos($hex, 'rgb') !== false);
		if($isRgb) {
			$rgbArr = utilsNbs::rgbToArray($hex);
			$isRgba = count($rgbArr) == 4;
			$hex = utilsNbs::rgbToHex($rgbArr);
		}
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

		if($isRgb) {
			$return = utilsNbs::hexToRgb( $return );
			if($isRgba) {	// Don't forget about alpha chanel
				$return[] = $rgbArr[ 3 ];
			}
			$return = ($isRgba ? 'rgba' : 'rgb'). '('. implode(',', $return). ')';
		}
		return $return;
	}
	public function __nbsComputeBgStylesArr( $key ) {
		$res = array();
		if(isset($this->octo['params'][$key. '_color']) && !empty($this->octo['params'][$key. '_color'])) {
			$res['background-color'] = $this->octo['params'][$key. '_color'];
		}
		if(isset($this->octo['params'][$key. '_img']) && !empty($this->octo['params'][$key. '_img'])) {
			$res['background-image'] = 'url(\''. $this->octo['params'][$key. '_img']. '\')';

			if(isset($this->octo['params'][$key. '_img_pos']) && !empty($this->octo['params'][$key. '_img_pos'])) {
				switch( $this->octo['params'][$key. '_img_pos'] ) {
					case 'stretch':
						$res['background-position'] = 'center center';
						$res['background-repeat'] = 'no-repeat';
						$res['background-attachment'] = 'fixed';
						$res['-webkit-background-size'] = 'cover';
						$res['-moz-background-size'] = 'cover';
						$res['-o-background-size'] = 'cover';
						$res['background-size'] = 'cover';
						break;
					case 'center':
						$res['background-position'] = 'center center';
						$res['background-repeat'] = 'no-repeat';
						$res['background-attachment'] = 'scroll';
						$res['-webkit-background-size'] = 'auto';
						$res['-moz-background-size'] = 'auto';
						$res['-o-background-size'] = 'auto';
						$res['background-size'] = 'auto';
						break;
					case 'tile':
						$res['background-position'] = 'left top';
						$res['background-repeat'] = 'repeat';
						$res['background-attachment'] = 'scroll';
						$res['-webkit-background-size'] = 'auto';
						$res['-moz-background-size'] = 'auto';
						$res['-o-background-size'] = 'auto';
						$res['background-size'] = 'auto';
						break;
				}
			}
		}
		return $res;
	}
}
