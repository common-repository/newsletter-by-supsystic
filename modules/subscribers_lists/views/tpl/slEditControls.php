<span id="nbsSubListMainControllsShell" style="float: right; padding-right: 95px;">
	<button class="button button-primary" id="nbsSubListSaveBtn" title="<?php _e('Save all changes', NBS_LANG_CODE)?>">
		<i class="fa fa-fw fa-save"></i>
		<?php _e('Save', NBS_LANG_CODE)?>
	</button>
	<?php if($this->subList['unique_id'] != NBS_WP_SUB_LIST) {?>
		<button class="button button-primary nbsSubListRemoveBtn">
			<i class="fa fa-fw fa-trash-o"></i>
			<?php _e('Delete', NBS_LANG_CODE)?>
		</button>
	<?php }?>
</span>
<div style="clear: both; height: 0;"></div>
