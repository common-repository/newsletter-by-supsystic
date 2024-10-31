<section class="nbsNewsletterMainOptSect">
	<span class="nbsOptLabel"><?php _e('When to send', NBS_LANG_CODE)?></span>
	<hr />
	<label class="nbsNlMainOptLbl">
		<?php echo htmlNbs::radiobutton('params[main][send_on]', array(
			'value' => 'immediately', 
			'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on', 'immediately', true)))?>
		<?php _e('Immediately - right after press "Send" button', NBS_LANG_CODE)?>
	</label>
	<label class="nbsNlMainOptLbl">
		<?php echo htmlNbs::radiobutton('params[main][send_on]', array(
			'value' => 'new_content', 
			'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on', 'new_content'),
			'attrs' => 'data-switch-block="sendOnNewContent"',
		))?>
		<?php _e('New content arrived', NBS_LANG_CODE)?>
	</label>
	<div class="nbsSwitchBlock" data-block-to-switch="sendOnNewContent" style="margin-left: 15px;">
		<label class="nbsNlMainOptLbl">
			<?php echo htmlNbs::radiobutton('params[main][send_on_new_content]', array(
				'value' => 'immediately', 
				'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on_new_content', 'immediately', true),
			))?>
			<?php _e('Immediately', NBS_LANG_CODE)?>
		</label>
		<label class="nbsNlMainOptLbl">
			<?php echo htmlNbs::radiobutton('params[main][send_on_new_content]', array(
				'value' => 'daily', 
				'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on_new_content', 'daily'),
				'attrs' => 'data-switch-block="sendOnNewContentDaily"',
			))?>
			<?php _e('Daily', NBS_LANG_CODE)?>
		</label>
		<span class="nbsSwitchBlock nbsNlMainOptSwitchBlock" data-block-to-switch="sendOnNewContentDaily">
			<?php _e('at', NBS_LANG_CODE)?>
			<?php echo htmlNbs::selectbox('params[main][send_on_new_content_daily_time]', array(
				'attrs' => 'class="time-choosen"',
				'options' => $this->timeRange,
				'value' => (isset($this->newsletter['params']['main']['send_on_new_content_daily_time']) ? $this->newsletter['params']['main']['send_on_new_content_daily_time'] : ''),
			))?>
		</span>
		<label class="nbsNlMainOptLbl">
			<?php echo htmlNbs::radiobutton('params[main][send_on_new_content]', array(
				'value' => 'weekly', 
				'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on_new_content', 'weekly'),
				'attrs' => 'data-switch-block="sendOnNewContentWeekly"',
			))?>
			<?php _e('Weekly', NBS_LANG_CODE)?>
		</label>
		<span class="nbsSwitchBlock nbsNlMainOptSwitchBlock" data-block-to-switch="sendOnNewContentWeekly">
			<?php _e('on', NBS_LANG_CODE)?>
			<?php echo htmlNbs::selectbox('params[main][send_on_new_content_weekly_day]', array(
				'attrs' => 'class="choosen"',
				'options' => $this->weekDaysRange,
				'value' => (isset($this->newsletter['params']['main']['send_on_new_content_weekly_day']) ? $this->newsletter['params']['main']['send_on_new_content_weekly_day'] : ''),
			))?>
			<?php _e('at', NBS_LANG_CODE)?>
			<?php echo htmlNbs::selectbox('params[main][send_on_new_content_weekly_time]', array(
				'attrs' => 'class="time-choosen"',
				'options' => $this->timeRange,
				'value' => (isset($this->newsletter['params']['main']['send_on_new_content_weekly_time']) ? $this->newsletter['params']['main']['send_on_new_content_weekly_time'] : ''),
			))?>
		</span>
		<label class="nbsNlMainOptLbl">
			<?php echo htmlNbs::radiobutton('params[main][send_on_new_content]', array(
				'value' => 'monthly', 
				'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on_new_content', 'monthly'),
				'attrs' => 'data-switch-block="sendOnNewContentMonthly"',
			))?>
			<?php _e('Monthly', NBS_LANG_CODE)?>
		</label>
		<span class="nbsSwitchBlock nbsNlMainOptSwitchBlock" data-block-to-switch="sendOnNewContentMonthly">
			<?php _e('on', NBS_LANG_CODE)?>
			<?php echo htmlNbs::selectbox('params[main][send_on_new_content_monthly_day]', array(
				'attrs' => 'class="choosen"',
				'options' => $this->monthDaysRange,
				'value' => (isset($this->newsletter['params']['main']['send_on_new_content_monthly_day']) ? $this->newsletter['params']['main']['send_on_new_content_monthly_day'] : ''),
			))?>
			<?php _e('at', NBS_LANG_CODE)?>
			<?php echo htmlNbs::selectbox('params[main][send_on_new_content_monthly_time]', array(
				'attrs' => 'class="time-choosen"',
				'options' => $this->timeRange,
				'value' => (isset($this->newsletter['params']['main']['send_on_new_content_monthly_time']) ? $this->newsletter['params']['main']['send_on_new_content_monthly_time'] : ''),
			))?>
		</span>
	</div>
	<?php /*?><label class="nbsNlMainOptLbl">
		<?php echo htmlNbs::radiobutton('params[main][send_on]', array(
			'value' => 'new_subscriber', 
			'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on', 'new_subscriber'),
			'attrs' => 'data-switch-block="sendOnNewSubscriber"',
		))?>
		<?php _e('New User Subscribed', NBS_LANG_CODE)?>
	</label>
	<div class="nbsSwitchBlock" data-block-to-switch="sendOnNewSubscriber" style="margin-left: 15px;">
		<label class="nbsNlMainOptLbl">
			<?php echo htmlNbs::radiobutton('params[main][send_on_new_subscriber]', array(
				'value' => 'immediately', 
				'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on_new_subscriber', 'immediately', true),
			))?>
			<?php _e('Immediately', NBS_LANG_CODE)?>
		</label>
		<label class="nbsNlMainOptLbl">
			<?php echo htmlNbs::radiobutton('params[main][send_on_new_subscriber]', array(
				'value' => 'delay', 
				'checked' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'send_on_new_subscriber', 'delay'),
				'attrs' => 'data-switch-block="sendOnNewSubscriberDelay"',
			))?>
			<?php _e('After', NBS_LANG_CODE)?>
		</label>
		<span class="nbsSwitchBlock nbsNlMainOptSwitchBlock" data-block-to-switch="sendOnNewSubscriberDelay">
			<?php echo htmlNbs::text('params[main][send_on_new_subscriber_delay]', array(
				'value' => (isset($this->newsletter['params']['main']['send_on_new_subscriber_delay']) ? $this->newsletter['params']['main']['send_on_new_subscriber_delay'] : ''),
			))?>
			<?php ?>
			<?php echo htmlNbs::selectbox('params[main][send_on_new_subscriber_delay_type]', array(
				'attrs' => 'class="time-choosen"',
				'options' => array(
					'hours' => __('Hour(s)', NBS_LANG_CODE),
					'days' => __('Days(s)', NBS_LANG_CODE),
					'weeks' => __('Week(s)', NBS_LANG_CODE),
				),
				'value' => (isset($this->newsletter['params']['main']['send_on_new_subscriber_delay_type']) ? $this->newsletter['params']['main']['send_on_new_subscriber_delay_type'] : 'hours'),
			))?>
		</span>
	</div><?php */?><br />
</section>
<section class="nbsNewsletterMainOptSect">
	<span class="nbsOptLabel"><?php _e('Analytics', NBS_LANG_CODE)?></span>
	<hr />
	<label class="nbsNlMainOptLbl">
		<?php echo htmlNbs::checkboxHiddenVal('params[main][enb_open_track]', array(
			'value' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'enb_open_track', true, true)))?>
		<?php _e('Enable open email tracking', NBS_LANG_CODE)?>
	</label>
	<label class="nbsNlMainOptLbl">
		<?php echo htmlNbs::checkboxHiddenVal('params[main][enb_click_track]', array(
			'value' => htmlNbs::checkedOpt($this->newsletter['params']['main'], 'enb_click_track', true, true)))?>
		<?php _e('Enable click tracking', NBS_LANG_CODE)?>
	</label>
</section>