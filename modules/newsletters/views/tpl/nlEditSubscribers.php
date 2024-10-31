<table class="form-table nbsFullWidthTbl nbsNlSubscribersTbl">
	<tr>
		<th scope="row">
			<?php _e('Subscribe Lists to send to', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Select lists of Subscribers that will receive your Newsletter', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::selectlist('slid', array(
				'value' => $this->newsletter['slid'],
				'options' => $this->listsForSelect,
				'attrs' => 'class="chosen"',
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php _e('Total recipients Count', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By changing SUbscribers lists in field above - recepients cont will be changed too', NBS_LANG_CODE))?>"></i>
		</th>
		<td class="nbsNlRecipientsCnt"></td>
	</tr>
</table>
