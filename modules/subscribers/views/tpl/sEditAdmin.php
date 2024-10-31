<section>
	<div class="supsystic-item supsystic-panel" style="padding-left: 10px;">
		<div id="containerWrapper">
			<form id="nbsSubFrm">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e('Username', NBS_LANG_CODE)?></th>
						<td><?php echo htmlNbs::text('username', array(
								'value' => ($this->subscriber ? $this->subscriber['username'] : ''),
								'required' => true,
							))?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Email', NBS_LANG_CODE)?></th>
						<td><?php echo htmlNbs::text('email', array(
								'value' => ($this->subscriber ? $this->subscriber['email'] : ''),
								'required' => true,
							))?></td>
					</tr>
					<?php if(isset($this->subscribedForm) 
						&& isset($this->subscribedForm['params']['fields']) 
						&& !empty($this->subscribedForm['params']['fields'])
					) {
						foreach($this->subscribedForm['params']['fields'] as $f) {
							if(in_array($f['name'], array('username', 'email'))) continue;
							if(in_array($f['html'], array('recaptcha', 'hidden', 'submit', 'reset', 'htmldelim', 'googlemap'))) continue;
							// TODO: create all variable possible field types here, not just show it's as HTML - take code from formsViewNbs::generateFields
					?>
					<tr>
						<th scope="row"><?php echo (isset($f['label']) & !empty($f['label']) ? $f['label'] : $f['placeholder']);?></th>
						<td><?php 
							$f['value'] = $this->subscriber && isset($this->subscriber['all_data'][ $f['name'] ]) 
								? $this->subscriber['all_data'][ $f['name'] ] 
								: false;
							echo frameNbs::_()->getModule('forms')->getView()->generateField( $f, $this->subscribedForm, array('fieldPrefName' => 'all_data') );
						?></td>
					</tr>
					<?php
						}
					}?>
					<?php if(isset($this->subscribedPopUp) 
						&& !empty($this->subscribedPopUp)
						&& isset($this->subscribedPopUp['params']['tpl']['sub_fields']) 
						&& !empty($this->subscribedPopUp['params']['tpl']['sub_fields'])
					) {
						foreach($this->subscribedPopUp['params']['tpl']['sub_fields'] as $k => $f) {
							if(in_array($k, array('name', 'email'))) continue;
							if(in_array($f['html'], array('mailchimp_lists', 'hidden', 'mailchimp_lists', 'mailchimp_groups_list', 'password'))) continue;
							// TODO: create all variable possible field types here, not just show it's as HTML - take code from formsViewNbs::generateFields
					?>
					<tr>
						<th scope="row"><?php echo (isset($f['label']) & !empty($f['label']) ? $f['label'] : $f['placeholder']);?></th>
						<td><?php 
							$htmlType = $f['html'];
							$htmlParams = array(
								'placeholder' => $f['label'],
							);
							$htmlParams['value'] = $this->subscriber && isset($this->subscriber['all_data'][ $k ]) 
							? $this->subscriber['all_data'][ $k ] 
							: false;
							if($htmlType == 'selectbox' && isset($f['options']) && !empty($f['options'])) {
								$htmlParams['options'] = array();
								foreach($f['options'] as $opt) {
									$htmlParams['options'][ $opt['name'] ] = isset($opt['label']) ? $opt['label'] : $opt['name'];
								}
							}
							echo htmlPps::$htmlType('all_data['. $k. ']', $htmlParams);
						?></td>
					</tr>
					<?php
						}
						echo htmlNbs::hidden('all_data[popup_id]', array('value' => $this->subscribedPopUp['id']));
					}?>
					<tr>
						<th scope="row"><?php _e('Status', NBS_LANG_CODE)?></th>
						<td><?php echo htmlNbs::selectbox('status', array(
								'value' => ($this->subscriber ? $this->subscriber['status'] : 0),
								'options' => $this->statusListForSelect
							))?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Lists', NBS_LANG_CODE)?></th>
						<td><?php echo htmlNbs::selectlist('slid', array(
							'value' => ($this->subscriber ? $this->subscriber['slid'] : false),
							'options' => $this->listsForSelect,
							'attrs' => 'class="chosen" data-placeholder="'. __('Select some Lists', NBS_LANG_CODE). '"',
						))?></td>
					</tr>
				</table>
				<?php echo htmlNbs::hidden('mod', array('value' => 'subscribers'))?>
				<?php echo htmlNbs::hidden('action', array('value' => 'save'))?>
				<?php echo htmlNbs::hidden('id', array('value' => ($this->subscriber ? $this->subscriber['id'] : 0)))?>
			</form>
		</div>
	</div>
</section>