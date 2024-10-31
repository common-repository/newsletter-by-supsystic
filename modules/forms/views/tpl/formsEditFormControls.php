&laquo;<span id="nbsFormEditableLabelShell" title="<?php _e('Click to Edit', NBS_LANG_CODE)?>">
	<span id="nbsFormEditableLabel"><?php echo $this->form['label']?></span>
	<?php echo htmlNbs::text('form_label', array(
		'attrs' => 'id="nbsFormEditableLabelTxt"'
	))?>
	<i id="nbsFormLabelEditMsg" class="fa fa-fw fa-pencil"></i>
</span>&raquo;&nbsp;
<span class="nbsFormShowMethodSelectionShell">
	<?php echo htmlNbs::selectbox('shortcode_example', array('options' => array(
			'shortcode' => __('Shortcode', NBS_LANG_CODE),
			'php_code' => __('PHP code', NBS_LANG_CODE),
			'widget' => __('Widget', NBS_LANG_CODE),
			//'popup' => __('PopUp', NBS_LANG_CODE),
		), 'attrs' => 'class="chosen" style="width:100px;" id="nbsFormShortcodeExampleSel"',
	))?>:
	<span class="nbsFormWhereShowBlock" data-for="shortcode">
		<?php echo htmlNbs::text('nbsCopyTextCode', array(
			'value' => esc_html('['. NBS_FORM_SHORTCODE. ' id='. $this->form['id']. ']'),
			'attrs' => 'class="nbsCopyTextCode"'));?>
	</span>
	<span class="nbsFormWhereShowBlock" data-for="php_code">
		<?php echo htmlNbs::text('nbsCopyTextCode', array(
			'value' => esc_html('<?php echo do_shortcode("['. NBS_FORM_SHORTCODE. ' id=\''. $this->form['id']. '\']");?>'),
			'attrs' => 'class="nbsCopyTextCode"'));?>
	</span>
	<span class="nbsFormWhereShowBlock" data-for="widget">
		<a target="_blank" class="button" href="<?php echo admin_url('widgets.php')?>"><?php _e('Add Subscribe Form Widget', NBS_LANG_CODE)?></a>
	</span>
	<?php /*?><span class="nbsFormWhereShowBlock" data-for="popup">
		<?php if($this->popupSupported) {
			printf(__('<a href="%s" target="_blank" class="button">Select your Form</a> in any PopUp', NBS_LANG_CODE), $this->popupSelectUrl);
		} else {
			printf(__('You need to have <a href="%s" target="_blank" class="button">installed PopUp plugin</a> to use this feature', NBS_LANG_CODE), admin_url('plugin-install.php?tab=search&s=PopUp+by+Supsystic'));
		}?>
	</span><?php */ ?>
</span>
<span id="nbsFormMainControllsShell" style="float: right; padding-right: 95px;">
	<button class="button button-primary nbsFormSaveBtn" title="<?php _e('Save all changes', NBS_LANG_CODE)?>">
		<i class="fa fa-fw fa-save"></i>
		<?php _e('Save', NBS_LANG_CODE)?>
	</button>
	<button class="button button-primary nbsFormCloneBtn" title="<?php _e('Clone to New Form', NBS_LANG_CODE)?>">
		<i class="fa fa-fw fa-files-o"></i>
		<?php _e('Clone', NBS_LANG_CODE)?>
	</button>
	<button class="button button-primary nbsFormPreviewBtn">
		<i class="fa fa-fw fa-eye"></i>
		<?php _e('Preview', NBS_LANG_CODE)?>
	</button>
	<?php /*It's working from shortcode or widget only - so no need to switch it's active status*/ ?>
	<?php /*?><button class="button button-primary nbsFormSwitchActive" data-txt-off="<?php _e('Turn Off', NBS_LANG_CODE)?>" data-txt-on="<?php _e('Turn On', NBS_LANG_CODE)?>">
		<i class="fa fa-fw"></i>
		<span></span>
	</button><?php */?>
	<button class="button button-primary nbsFormRemoveBtn">
		<i class="fa fa-fw fa-trash-o"></i>
		<?php _e('Delete', NBS_LANG_CODE)?>
	</button>
</span>
<div style="clear: both; height: 0;"></div>
<div id="nbsFormSaveAsCopyWnd" style="display: none;">
	<form id="nbsFormSaveAsCopyForm">
		<label>
			<?php _e('New Name', NBS_LANG_CODE)?>:
			<?php echo htmlNbs::text('copy_label', array('value' => $this->form['label']. ' '. __('Copy', NBS_LANG_CODE), 'required' => true))?>
		</label>
		<div id="nbsFormSaveAsCopyMsg"></div>
		<?php echo htmlNbs::hidden('mod', array('value' => 'forms'))?>
		<?php echo htmlNbs::hidden('action', array('value' => 'saveAsCopy'))?>
		<?php echo htmlNbs::hidden('id', array('value' => $this->form['id']))?>
	</form>
</div>
