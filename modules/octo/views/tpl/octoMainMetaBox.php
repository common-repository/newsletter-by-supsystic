<div class="nbsActivateForPostShell" <?php if($this->isPostConverted) {?>style="display: none;"<?php }?>>
	<div class="misc-pub-section">
		<a href="#" class="button button-primary nbsActivatePostBtn" data-pid="<?php echo $this->post->ID?>">
			<?php printf(__('Activate %s', NBS_LANG_CODE), NBS_OUR_NAME)?>
		</a>
	</div>
</div>
<div class="nbsPostSettingsShell" <?php if(!$this->isPostConverted) {?>style="display: none;"<?php }?>>
	<div class="nbsPostSettingsContent">
		<div class="misc-pub-section dashicons-screenoptions dashicons-before">
			<?php _e('Blocks usage')?>: <?php echo (string) $this->usedBlocksNumber;?>
		</div>
	</div>
	<div class="nbsPostSettingsFooter">
		<a href="#" class="nbsReturnPostFromNbso" data-pid="<?php echo $this->post->ID?>">
			<?php printf(__('Deactivate %s', NBS_LANG_CODE), NBS_OUR_NAME)?>
		</a>
		<a href="#" target="_blank" class="button button-primary nbsEditTplBtn" data-pid="<?php echo $this->post->ID?>">
			<?php _e('Build Page', NBS_LANG_CODE)?>
		</a>
		<div style="clear: both;"></div>
	</div>
</div>