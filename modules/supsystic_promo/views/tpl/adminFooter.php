<div class="nbsAdminFooterShell">
	<div class="nbsAdminFooterCell">
		<?php echo NBS_WP_PLUGIN_NAME?>
		<?php _e('Version', NBS_LANG_CODE)?>:
		<a target="_blank" href="http://wordpress.org/plugins/newsletters-by-supsystic/changelog/"><?php echo NBS_VERSION?></a>
	</div>
	<div class="nbsAdminFooterCell">|</div>
	<?php  if(!frameNbs::_()->getModule(implode('', array('l','ic','e','ns','e')))) {?>
	<div class="nbsAdminFooterCell">
		<?php _e('Go', NBS_LANG_CODE)?>&nbsp;<a target="_blank" href="<?php echo $this->getModule()->getMainLink();?>"><?php _e('PRO', NBS_LANG_CODE)?></a>
	</div>
	<div class="nbsAdminFooterCell">|</div>
	<?php } ?>
	<div class="nbsAdminFooterCell">
		<a target="_blank" href="http://wordpress.org/support/plugin/newsletters-by-supsystic"><?php _e('Support', NBS_LANG_CODE)?></a>
	</div>
	<div class="nbsAdminFooterCell">|</div>
	<div class="nbsAdminFooterCell">
		Add your <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/newsletters-by-supsystic?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on wordpress.org.
	</div>
</div>