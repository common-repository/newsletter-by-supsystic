<?php
class scheduleNbs extends moduleNbs {
	public function __construct($d) {
		parent::__construct($d);
		dispatcherNbs::addFilter('optionsDefine', array($this, 'addOpts'));
		dispatcherNbs::addAction('beforeSaveOpts', array($this, 'checkSavedOpts'));
	}
	public function init() {
		parent::init();
		add_filter('cron_schedules', array($this, 'addCronSchedules'));
		add_action('init', array($this, 'initMainSchedules'), 999);
	}
	public function initMainSchedules() {
		add_action('nbs_cron_queue', array($this, 'checkQueue'));
		if(!wp_next_scheduled('nbs_cron_queue')) {
			$scheduleFreq = frameNbs::_()->getModule('options')->get('emails_queue_freq');
			wp_schedule_event(time(), $scheduleFreq, 'nbs_cron_queue');
		}
	}
	public function addCronSchedules($param) {
		$min = 60;
		$hour = 60 * $min;
		return array_merge($param, array(
			'one_min' => array(
				'interval' => $min,
				'display' => __('Once every minute', NBS_LANG_CODE)
			),
			'two_min' => array(
				'interval' => 2 * $min,
				'display' => __('Once every two minutes', NBS_LANG_CODE)
			),
			'ten_min' => array(
				'interval' => 10 * $min,
				'display' => __('Once every ten minutes', NBS_LANG_CODE)
			),
			'thirty_min' => array(
				'interval' => 30 * $min,
				'display' => __('Once every thirty minutes', NBS_LANG_CODE)
			),
			'two_hours' => array(
				'interval' => 2 * $hour,
				'display' => __('Once every two hours', NBS_LANG_CODE)
			),
			'eachweek' => array(
				'interval' => 7 * 24 * $hour,
				'display' => __('Once a week', NBS_LANG_CODE)
			),
			'each28days' => array(
				'interval' => 28 * 24 * $hour,
				'display' => __('Once every 28 days', NBS_LANG_CODE)
			),
		));
	}
	public function addOpts( $options ) {
		$options['general']['opts'] = array_merge($options['general']['opts'], array(
			'emails_per_queue' => array('label' => __('Emails per Queue', NBS_LANG_CODE), 'desc' => esc_html(__('Plugin is sending emails by batches - this is made because of big amount of emails can just stop your server to work.', NBS_LANG_CODE)), 'def' => '80', 'html' => 'text'),
			'emails_queue_freq' => array('label' => __('Emails Queue check Frequency', NBS_LANG_CODE), 'desc' => esc_html(__('', NBS_LANG_CODE)), 'def' => 'hourly', 'html' => 'selectbox', 'options' => array(
				'one_min' => __('Once every minute', NBS_LANG_CODE),
				'two_min' => __('Once every two minutes', NBS_LANG_CODE),
				'ten_min' => __('Once every ten minutes', NBS_LANG_CODE),
				'thirty_min' => __('Once every thirty minutes', NBS_LANG_CODE),
				'hourly' => __('Once every hour', NBS_LANG_CODE),
				'two_hours' => __('Once every two hours', NBS_LANG_CODE),
			)),
		));
		return $options;
	}
	public function checkQueue() {
		frameNbs::_()->getModule('log')->addLine('schedule::checkQueue - start');
		frameNbs::_()->getModule('queue')->check();
		frameNbs::_()->getModule('log')->addLine('schedule::checkQueue - stop');
	}
	public function checkSavedOpts($d) {
		if((isset($d['opt_values']['emails_per_queue']) && $d['opt_values']['emails_per_queue'] != frameNbs::_()->getModule('options')->get('emails_per_queue'))
			|| (isset($d['opt_values']['emails_queue_freq']) && $d['opt_values']['emails_queue_freq'] != frameNbs::_()->getModule('options')->get('emails_queue_freq'))
		) {
			wp_clear_scheduled_hook('nbs_cron_queue');
		}
	}
}

