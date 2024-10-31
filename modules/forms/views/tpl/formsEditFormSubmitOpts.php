<table class="form-table nbsFormSubmitOptsTbl" style="width: 100%">
	<tr>
		<th scope="row">
			<?php _e('Lists for Subscribe', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Select lists, where you subscriber will be added after submit Subscription form data..', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php if(!empty($this->subListsForSelect)) {?>
			<?php echo htmlNbs::selectlist('params[tpl][lists]', array(
				'value' => isset($this->form['params']['tpl']['lists']) ? $this->form['params']['tpl']['lists'] : false,
				'attrs' => 'class="chosen"',
				'options' => $this->subListsForSelect,
			))?>
			<?php } else {
				printf(__('You don\'t have any Subscribers list for now. <a href="%s" target="_blank" class="button">Create firs List</a> before selecting it here.', NBS_LANG_CODE), frameNbs::_()->getModule('options')->getTabUrl('subscribers_lists'));
			}?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Form sent message', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Message, that your users will see after success form submition.', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::text('params[tpl][form_sent_msg]', array('value' => $this->form['params']['tpl']['form_sent_msg']))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Form sent message color', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Text color for your Success message.', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::colorpicker('params[tpl][form_sent_msg_color]', array(
				'value' => (isset($this->form['params']['tpl']['form_sent_msg_color']) ? $this->form['params']['tpl']['form_sent_msg_color'] : '#4ae8ea'),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Send Confirm email', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By default Subscriber will be created with Disabled stats, enable this option - we link with Subscribe Confirm will be send to provided subscriber email, and user will click on this link - status will be changed to Confirmed.', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::checkboxHiddenVal('params[tpl][send_confirm]', array(
				'value' => (isset($this->form['params']['tpl']['send_confirm']) ? $this->form['params']['tpl']['send_confirm'] : 1),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Hide form after submit', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By default form will be hidden after successful submit, but you can disable this here - and after submit form will be just cleared.', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::checkboxHiddenVal('params[tpl][hide_on_submit]', array(
				'value' => (isset($this->form['params']['tpl']['hide_on_submit']) ? $this->form['params']['tpl']['hide_on_submit'] : 1),
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Redirect after submit', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('If you want - you can redirect user after Form was submitted. Just enter required Redirect URL here - and each time after Form will be submitted - user will be redirected to that URL. Just leave this field empty - if you don\'t need this functionality in your Form.', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::text('params[tpl][redirect_on_submit]', array(
				'value' => (isset($this->form['params']['tpl']['redirect_on_submit']) ? esc_url( $this->form['params']['tpl']['redirect_on_submit'] ) : ''),
				'attrs' => 'placeholder="http://example.com" style="width: 100%;"',
			))?><br />
			<label>
				<?php echo htmlNbs::checkbox('params[tpl][redirect_on_submit_new_wnd]', array(
					'checked' => htmlNbs::checkedOpt($this->form['params']['tpl'], 'redirect_on_submit_new_wnd')))?>
				<?php _e('Open in a new window (tab)', NBS_LANG_CODE)?>
			</label>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Test Email Function', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Email delivery depends from your server configuration. For some cases - you and your subscribers can not receive emails just because email on your server is not working correctly. You can easy test it here - by sending test email. If you receive it - then it means that email functionality on your server works well. If not - this means that it is not working correctly and you should contact your hosting provider with this issue and ask them to setup email functionality for you on your server.', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::email('params[tpl][test_email]', array(
				'value' => (isset($this->form['params']['tpl']['test_email']) ? $this->form['params']['tpl']['test_email'] : $this->adminEmail),
			))?>
			<a href="#" class="nbsTestEmailFuncBtn button">
				<i class="fa fa-paper-plane"></i>
				<?php _e('Send Test Email', NBS_LANG_CODE)?>
			</a>
			<div class="nbsTestEmailWasSent" style="display: none;">
				<?php _e('Email was sent. Now check your email inbox / spam folders for test mail. If you don’t find it - it means that your server can\'t send emails - and you need to contact your hosting provider with this issue.', NBS_LANG_CODE)?>
			</div>
		</td>
	</tr>
</table>
