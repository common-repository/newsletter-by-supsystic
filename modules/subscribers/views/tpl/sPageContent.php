<form method="post" action="" class="nbsSubUpdateProfileFrm">
	<h2><?php _e('Your data', NBS_LANG_CODE)?></h2>
	<table width="100%">	
		<tr>
			<th><label for="nbsSubField_email"><?php _e('Email', NBS_LANG_CODE)?></label></th>
			<td><?php echo htmlNbs::email('fields[email]', array(
				'value' => $this->subscriber['email'],
				'required' => true,
				'attrs' => 'id="nbsSubField_email"',
			))?></td>
		</tr>
		<?php if(isset($this->subscriber['all_data']) && !empty($this->subscriber['all_data'])) { ?>
			<?php //TODO: Add here possibility to edit All subscribers data ?>
		<?php }?>
	</table>
	<?php if(!empty($this->subLists)) {?>
	<h2><?php _e('Your Subscriptions', NBS_LANG_CODE)?></h2>
	<table width="100%">
		<?php foreach($this->subLists as $list) { ?>
		<tr>
			<th><label for="nbsSlidCheck_<?php echo $list['id'];?>"><?php echo $list['label']?></label></th>
			<td><?php echo htmlNbs::checkbox('slid['. $list['id']. ']', array(
				'checked' => ($this->subscriber['slid'] && in_array($list['id'], $this->subscriber['slid'])),
				'attrs' => 'id="nbsSlidCheck_'. $list['id']. '"',
			))?></td>
		</tr>
		<?php }?>
	</table>
	<?php }?>
	<?php echo htmlNbs::hidden('mod', array('value' => 'subscribers'))?>
	<?php echo htmlNbs::hidden('action', array('value' => 'updateProfile'))?>
	<?php echo htmlNbs::hidden('id', array('value' => $this->subscriber['id']))?>
	<?php echo htmlNbs::hidden('_wpnonce', array('value' => wp_create_nonce('profile-'. $this->subscriber['id'])))?>
	<?php echo htmlNbs::submit('save', array(
		'value' => __('Save changes', NBS_LANG_CODE),
		'attrs' => 'class="button button-primary"',
	))?>
	<span class="nbsSubUpdateProfileMsg"></span>
</form>