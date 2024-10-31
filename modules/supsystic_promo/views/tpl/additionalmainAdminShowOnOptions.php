<label class="supsystic-tooltip-right" title="<?php echo esc_html(sprintf(__('Show when user tries to exit from your site. <a target="_blank" href="%s">Check example.</a>', NBS_LANG_CODE), 'http://supsystic.com/exit-newsletters/?utm_source=plugin&utm_medium=onexit&utm_campaign=newsletters'))?>">
	<a target="_blank" href="<?php echo $this->promoLink?>" class="sup-promolink-input">
		<?php echo htmlNbs::radiobutton('promo_show_on_opt', array(
			'value' => 'on_exit_promo',
			'checked' => false,
		))?>
		<?php _e('On Exit from Site', NBS_LANG_CODE)?>
	</a>
	<a target="_blank" href="<?php echo $this->promoLink?>"><?php _e('Available in PRO')?></a>
</label>