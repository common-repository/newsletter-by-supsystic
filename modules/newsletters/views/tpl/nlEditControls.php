&laquo;<span id="nbsNewsletterEditableLabelShell" title="<?php _e('Click to Edit', NBS_LANG_CODE)?>">
	<span id="nbsNewsletterEditableLabel"><?php echo $this->newsletter['label']?></span>
	<?php echo htmlNbs::text('newsletter_label', array(
		'attrs' => 'id="nbsNewsletterEditableLabelTxt"'
	))?>
	<i id="nbsNewsletterLabelEditMsg" class="fa fa-fw fa-pencil"></i>
</span>&raquo;&nbsp;
<span id="nbsNewsletterMainControllsShell" style="float: right; padding-right: 95px;">
	<button class="button button-primary nbsNewsletterSaveBtn" title="<?php _e('Save all changes', NBS_LANG_CODE)?>">
		<i class="fa fa-fw fa-save"></i>
		<?php _e('Save', NBS_LANG_CODE)?>
	</button>
	<a href="<?php echo $this->editOctoUrl;?>" target="_blank" title="<?php _e('Edit in our Super Builder', NBS_LANG_CODE)?>" class="button button-primary">
		<i class="fa fa-fw fa-pencil"></i>
		<?php _e('Edit Template', NBS_LANG_CODE)?>
	</a>
	<button class="button button-primary nbsNewsletterSendBtn" data-start-txt="<?php _e('Are you sure want to start sending?', NBS_LANG_CODE)?>" title="<?php _e('Send to all selected Subscribers', NBS_LANG_CODE)?>">
		<i class="fa fa-fw fa-paper-plane-o"></i>
		<?php _e('Start Sending', NBS_LANG_CODE)?>
	</button>
	<button class="button button-primary nbsNewsletterCloneBtn" title="<?php _e('Clone to New Newsletter', NBS_LANG_CODE)?>">
		<i class="fa fa-fw fa-files-o"></i>
		<?php _e('Clone', NBS_LANG_CODE)?>
	</button>
	<a href="<?php echo $this->nbsAddNewUrl. '&change_for='. $this->newsletter['id']?>" class="button button-primary nbsNewsletterSelectTpl">
		<i class="fa fa-fw fa-repeat"></i>
		<?php _e('Change Template', NBS_LANG_CODE)?>
	</a>
	<button class="button button-primary nbsNewsletterPreviewBtn">
		<i class="fa fa-fw fa-eye"></i>
		<?php _e('Preview', NBS_LANG_CODE)?>
	</button>
	<button class="button button-primary nbsNewsletterRemoveBtn">
		<i class="fa fa-fw fa-trash-o"></i>
		<?php _e('Delete', NBS_LANG_CODE)?>
	</button>
</span>
<div style="clear: both; height: 0;"></div>
<div id="nbsNewsletterSaveAsCopyWnd" style="display: none;">
	<form id="nbsNewsletterSaveAsCopyNewsletter">
		<label>
			<?php _e('New Name', NBS_LANG_CODE)?>:
			<?php echo htmlNbs::text('copy_label', array('value' => $this->newsletter['label']. ' '. __('Copy', NBS_LANG_CODE), 'required' => true))?>
		</label>
		<div id="nbsNewsletterSaveAsCopyMsg"></div>
		<?php echo htmlNbs::hidden('mod', array('value' => 'newsletters'))?>
		<?php echo htmlNbs::hidden('action', array('value' => 'saveAsCopy'))?>
		<?php echo htmlNbs::hidden('id', array('value' => $this->newsletter['id']))?>
	</form>
</div>
