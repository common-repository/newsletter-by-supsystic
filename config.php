<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('NBS_WPLANG', 'en_GB');
    } else {
        define('NBS_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('NBS_PLUG_NAME', basename(dirname(__FILE__)));
    define('NBS_DIR', WP_PLUGIN_DIR. DS. NBS_PLUG_NAME. DS);
    define('NBS_TPL_DIR', NBS_DIR. 'tpl'. DS);
    define('NBS_CLASSES_DIR', NBS_DIR. 'classes'. DS);
    define('NBS_TABLES_DIR', NBS_CLASSES_DIR. 'tables'. DS);
	define('NBS_HELPERS_DIR', NBS_CLASSES_DIR. 'helpers'. DS);
    define('NBS_LANG_DIR', NBS_DIR. 'languages'. DS);
    define('NBS_IMG_DIR', NBS_DIR. 'img'. DS);
    define('NBS_TEMPLATES_DIR', NBS_DIR. 'templates'. DS);
    define('NBS_MODULES_DIR', NBS_DIR. 'modules'. DS);
    define('NBS_FILES_DIR', NBS_DIR. 'files'. DS);
    define('NBS_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

	define('NBS_PLUGINS_URL', plugins_url());
	define('NBS_SITE_ROOT_URL', get_bloginfo('wpurl'));
    define('NBS_SITE_URL', NBS_SITE_ROOT_URL. '/');
    define('NBS_JS_PATH', NBS_PLUGINS_URL. '/'. NBS_PLUG_NAME. '/js/');
    define('NBS_CSS_PATH', NBS_PLUGINS_URL. '/'. NBS_PLUG_NAME. '/css/');
    define('NBS_IMG_PATH', NBS_PLUGINS_URL. '/'. NBS_PLUG_NAME. '/img/');
    define('NBS_MODULES_PATH', NBS_PLUGINS_URL. '/'. NBS_PLUG_NAME. '/modules/');
    define('NBS_TEMPLATES_PATH', NBS_PLUGINS_URL. '/'. NBS_PLUG_NAME. '/templates/');
    define('NBS_JS_DIR', NBS_DIR. 'js/');

    define('NBS_URL', NBS_SITE_URL);

    define('NBS_LOADER_IMG', NBS_IMG_PATH. 'loading.gif');
	define('NBS_TIME_FORMAT', 'H:i:s');
    define('NBS_DATE_DL', '/');
    define('NBS_DATE_FORMAT', 'm/d/Y');
    define('NBS_DATE_FORMAT_HIS', 'm/d/Y ('. NBS_TIME_FORMAT. ')');
    define('NBS_DATE_FORMAT_JS', 'mm/dd/yy');
    define('NBS_DATE_FORMAT_CONVERT', '%m/%d/%Y');
	define('NBS_DB_DATE_FORMAT', 'Y-m-d H:i:s');
    define('NBS_WPDB_PREF', $wpdb->prefix);
    define('NBS_DB_PREF', 'nbs_');
    define('NBS_MAIN_FILE', 'nbs.php');

    define('NBS_DEFAULT', 'default');
    define('NBS_CURRENT', 'current');
	
	define('NBS_EOL', "\n");    
    
    define('NBS_PLUGIN_INSTALLED', true);
    define('NBS_VERSION', '1.5.6');
    define('NBS_USER', 'user');
    
    define('NBS_CLASS_PREFIX', 'nbsc');     
    define('NBS_FREE_VERSION', false);
	define('NBS_TEST_MODE', true);
    
    define('NBS_SUCCESS', 'Success');
    define('NBS_FAILED', 'Failed');
	define('NBS_ERRORS', 'nbsErrors');
	
	define('NBS_ADMIN',	'admin');
	define('NBS_LOGGED','logged');
	define('NBS_GUEST',	'guest');
	
	define('NBS_ALL',		'all');
	
	define('NBS_METHODS',		'methods');
	define('NBS_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code
	 */
	define('NBS_CODE', 'nbs');

	define('NBS_LANG_CODE', 'newsletter-by-supsystic');
	/**
	 * Plugin name
	 */
	define('NBS_WP_PLUGIN_NAME', 'Newsletter by Supsystic');
	/**
	 * Custom defined for plugin
	 */
	define('NBS_COMMON', 'common');
	define('NBS_FB_LIKE', 'fb_like');
	define('NBS_VIDEO', 'video');
	define('NBS_IFRAME', 'iframe');
	define('NBS_SIMPLE_HTML', 'simple_html');
	define('NBS_PDF', 'pdf');
	define('NBS_AGE_VERIFY', 'age_verify');
	define('NBS_FULL_SCREEN', 'full_screen');
	define('NBS_LOGIN_REGISTER', 'login_register');
	define('NBS_BAR', 'bar');
	define('NBS_FORM_SHORTCODE', 'supsystic-newsletter-form');
	/**
	 * Wordpress default subscribers list Unique ID
	 */
	define('NBS_WP_SUB_LIST', 'wpsub');
	/**
	 * Shortcode for subscribers page content	 * 
	 */
	define('NBS_SUBSCRIBERS_PAGE_CONTENT_SHORTCODE', 'supsystic-subscribers-page');
	/**
	 * Default theme width
	 */
	define('NBS_DEF_WIDTH', 600);
	define('NBS_DEF_WIDTH_UNITS', 'px');

