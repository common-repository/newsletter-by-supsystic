<span id="nbsSubMainControllsShell" style="float: right; padding-right: 95px;">
	<button class="button button-primary" id="nbsSubSaveBtn" title="<?php _e('Save all changes', NBS_LANG_CODE)?>">
		<i class="fa fa-fw fa-save"></i>
		<?php _e('Save', NBS_LANG_CODE)?>
	</button>
	<button class="button button-primary" 
			id="nbsSubRemoveBtn" 
			title="<?php _e('Remove current subscriber from database', NBS_LANG_CODE)?>"
			style="<?php echo (empty($this->currentId) ? 'display: none;' : '')?>"
	>
		<i class="fa fa-fw fa-trash-o"></i>
		<?php _e('Delete', NBS_LANG_CODE)?>
	</button>
</span>
<div style="clear: both; height: 0;"></div>
