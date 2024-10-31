<p class="alert alert-danger">
	<?php //printf(__('Edit this ONLY if you know basics of HTML, CSS and have been acquainted with the rules of template editing described <a target="_blank" href="%s">here</a>', NBS_LANG_CODE), 'http://supsystic.com/edit-form-html-css-code/')?>
	<?php _e('Edit this ONLY if you know basics of HTML and CSS', NBS_LANG_CODE)?>
</p>
<fieldset>
	<legend><?php _e('Field Wrapper')?></legend>
	<?php echo htmlNbs::textarea('params[tpl][field_wrapper]', array('value' => esc_html($this->form['params']['tpl']['field_wrapper']), 'attrs' => 'id="nbsFormFieldWrapperEditor"'))?>
</fieldset>
<fieldset>
	<legend><?php _e('CSS code')?></legend>
	<?php echo htmlNbs::textarea('css', array('value' => esc_html($this->form['css']), 'attrs' => 'id="nbsFormCssEditor"'))?>
</fieldset>
<fieldset>
	<legend><?php _e('HTML code')?></legend>
	<?php echo htmlNbs::textarea('html', array('value' => esc_html($this->form['html']), 'attrs' => 'id="nbsFormHtmlEditor"'))?>
</fieldset>