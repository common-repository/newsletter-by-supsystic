<?php
/**
 * Plugin Name: Newsletter by Supsystic
 * Description: Simple email newsletter plugin with unlimited subscribers and newsletters. Create, send and track mass mail. Newsletter templates drag-drop builder
 * Version: 1.5.6
 * Author: supsystic.com
 * Author URI: https://supsystic.com
 * Text Domain: newsletter-by-supsystic
 * Domain Path: /languages
 **/
    /**
	 * Base config constants and functions
	 */
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
	/**
	 * Connect all required core classes
	 */
    importClassNbs('dbNbs');
    importClassNbs('installerNbs');
    importClassNbs('baseObjectNbs');
    importClassNbs('moduleNbs');
	importClassNbs('moduleWidgetNbs');
    importClassNbs('modelNbs');
    importClassNbs('viewNbs');
    importClassNbs('controllerNbs');
    importClassNbs('helperNbs');
    importClassNbs('dispatcherNbs');
    importClassNbs('fieldNbs');
    importClassNbs('tableNbs');
    importClassNbs('frameNbs');
	/**
	 * @deprecated since version 1.0.1
	 */
    importClassNbs('langNbs');
    importClassNbs('reqNbs');
    importClassNbs('uriNbs');
    importClassNbs('htmlNbs');
    importClassNbs('responseNbs');
    importClassNbs('fieldAdapterNbs');
    importClassNbs('validatorNbs');
    importClassNbs('errorsNbs');
    importClassNbs('utilsNbs');
    importClassNbs('modInstallerNbs');
	importClassNbs('installerDbUpdaterNbs');
	importClassNbs('dateNbs');
	/**
	 * Check plugin version - maybe we need to update database, and check global errors in request
	 */
    installerNbs::update();
    errorsNbs::init();
    /**
	 * Start application
	 */
    frameNbs::_()->parseRoute();
    frameNbs::_()->init();
    frameNbs::_()->exec();
	
	//var_dump(frameNbs::_()->getActivationErrors()); exit();
