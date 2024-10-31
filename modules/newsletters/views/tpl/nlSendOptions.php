<table class="form-table nbsFullWidthTbl">
	<tr>
		<th scope="row">
			<?php _e('Subject', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Subjectfor your Newsletter email', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::text('params[send][subject]', array(
				'value' => esc_html(isset($this->newsletter['params']['send']['subject']) ? $this->newsletter['params']['send']['subject'] : $this->newsletter['label']),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Send From Name', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Send From Name parameter in your Newsletter', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::text('params[send][from_name]', array(
				'value' => esc_html(isset($this->newsletter['params']['send']['from_name']) ? $this->newsletter['params']['send']['from_name'] : $this->siteName),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Send From Email', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Send From Email parameter in your Newsletter', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::email('params[send][from_email]', array(
				'value' => esc_html(isset($this->newsletter['params']['send']['from_email']) ? $this->newsletter['params']['send']['from_email'] : $this->adminEmail),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Reply To Name', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Reply To Name parameter in your Newsletter', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::text('params[send][reply_to_name]', array(
				'value' => esc_html(isset($this->newsletter['params']['send']['reply_to_name']) ? $this->newsletter['params']['send']['reply_to_name'] : $this->siteName),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Reply To Email', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Reply To Email parameter in your Newsletter', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::email('params[send][reply_to_email]', array(
				'value' => esc_html(isset($this->newsletter['params']['send']['reply_to_email']) ? $this->newsletter['params']['send']['reply_to_email'] : $this->adminEmail),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Return Path Email', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Email address where delivery error messages are sent by mailing systems (eg. mailbox full, invalid address, ...)', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::email('params[send][return_path_email]', array(
				'value' => esc_html(isset($this->newsletter['params']['send']['return_path_email']) ? $this->newsletter['params']['send']['return_path_email'] : ''),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Send Test Email', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Send test email - to check that everything is working as it should', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::email('params[tpl][send_test]', array(
				'value' => esc_html(isset($this->newsletter['params']['tpl']['send_test']) ? $this->newsletter['params']['tpl']['send_test'] : $this->adminEmail),
				'attrs' => 'class="nbsAutoWidth"'
			))?>
			<a href="#" class="button nbsSendTestBtn">
				<i class="fa fa-paper-plane-o"></i>
				<?php _e('Send', NBS_LANG_CODE)?>
			</a>
			<span class="nbsSendTestMsg"></span>
		</td>
	</tr>
</table>
