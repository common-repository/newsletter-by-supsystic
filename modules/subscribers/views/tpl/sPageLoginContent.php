<form method="post" action="" class="nbsSubLoginFrm">
	<h2><?php _e('Login to your profile', NBS_LANG_CODE)?></h2>
	<table width="100%">	
		<tr>
			<th><?php _e('Email', NBS_LANG_CODE)?></th>
			<td><?php echo htmlNbs::email('email', array(
				'required' => true,
			))?></td>
		</tr>
	</table>
	<?php echo htmlNbs::hidden('mod', array('value' => 'subscribers'))?>
	<?php echo htmlNbs::hidden('action', array('value' => 'sendLoginUrl'))?>
	<?php echo htmlNbs::hidden('_wpnonce', array('value' => wp_create_nonce('send-login-url')))?>
	<?php echo htmlNbs::submit('save', array(
		'value' => __('Login', NBS_LANG_CODE),
		'attrs' => 'class="button button-primary"'
	))?>
	<span class="nbsSubLoginMsg"></span>
</form>