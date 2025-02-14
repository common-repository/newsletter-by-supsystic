<div id="nbsFormEditTabs">
	<section class="supsystic-bar supsystic-sticky sticky-padd-next sticky-save-width sticky-base-width-auto" data-prev-height="#supsystic-breadcrumbs" data-next-padding-add="15">
		<h3 class="nav-tab-wrapper" style="margin-bottom: 0px; margin-top: 12px;">
			<?php $i = 0;?>
			<?php foreach($this->tabs as $tKey => $tData) { ?>
				<?php
					$iconClass = 'nbs-edit-icon';
					if(isset($tData['avoid_hide_icon']) && $tData['avoid_hide_icon']) {
						$iconClass .= '-not-hide';	// We will just exclude it from selector to hide, jQuery.not() - make browser slow down in this case - so better don't use it
					}
				?>
				<a class="nav-tab <?php if($i == 0) { echo 'nav-tab-active'; }?>" href="#<?php echo $tKey?>">
					<?php if(isset($tData['fa_icon'])) { ?>
						<i class="<?php echo $iconClass?> fa <?php echo $tData['fa_icon']?>"></i>
					<?php } elseif(isset($tData['icon_content'])) { ?>
						<i class="<?php echo $iconClass?> fa"><?php echo $tData['icon_content']?></i>
					<?php }?>
					<span class="nbsFormTabTitle"><?php echo $tData['title']?></span>
				</a>
			<?php $i++; }?>
		</h3>
	</section>
	<section>
		<div class="supsystic-item supsystic-panel" style="padding-left: 10px;">
			<div id="containerWrapper">
				<form id="nbsFormEditForm">
					<?php foreach($this->tabs as $tKey => $tData) { ?>
						<div id="<?php echo $tKey?>" class="nbsTabContent">
							<?php echo $tData['content']?>
						</div>
					<?php }?>
					<?php if(isset($this->form['params']['opts_attrs'])) {?>
						<?php foreach($this->form['params']['opts_attrs'] as $optKey => $attr) {
							echo htmlNbs::hidden('params[opts_attrs]['. $optKey. ']', array('value' => $attr));
						}?>
					<?php }?>
					<?php echo htmlNbs::hidden('mod', array('value' => 'forms'))?>
					<?php echo htmlNbs::hidden('action', array('value' => 'save'))?>
					<?php echo htmlNbs::hidden('id', array('value' => $this->form['id']))?>
					<?php echo htmlNbs::nonceForAction('save')?>
				</form>
				<div style="clear: both;"></div>
				<div id="nbsFormPreview" style="">
					<iframe id="nbsFormPreviewFrame" width="" height="" frameborder="0" src="" style=""></iframe>
					<script type="text/javascript">
					jQuery('#nbsFormPreviewFrame').load(function(){
						if(typeof(nbsHidePreviewUpdating) === 'function')
							nbsHidePreviewUpdating();
						var $contentDoc = jQuery(this).contents()
						,	formShell = $contentDoc.find('.nbsFormShell')
						,	paddingSize = 40
						,	newWidth = (jQuery(this).get(0).contentWindow.document.body.scrollWidth + paddingSize)
						,	newHeight = (jQuery(this).get(0).contentWindow.document.body.scrollHeight + paddingSize)
						,	parentWidth = jQuery('#nbsFormPreview').width()
						,	widthMeasure = jQuery('#nbsFormEditForm').find('[name="params[tpl][width_measure]"]:checked').val();

						if(widthMeasure == '%') {
							newWidth = parentWidth;
						} else {
							if(newWidth > parentWidth) {
								newWidth = parentWidth;
							}
						}
						jQuery(this).width( newWidth+ 'px' );
						jQuery(this).height( newHeight+ 'px' );
						var top = 15
						,	left = 0;
						if(typeof(nbsForm) !== 'undefined') {
							var addMoveForms = [];	// Nothing here for now
							for(var i = 0; i < addMoveForms.length; i++) {
								if(nbsForm.id == addMoveForms[i].id 
									|| nbsForm.original_id == addMoveForms[i].id
								) {
									if(addMoveForms[i].top) {
										top = addMoveForms[i].top;
									}
									if(addMoveForms[i].left) {
										left = addMoveForms[i].left;
									}
								}
							}
						}
						formShell.css({
							'position': 'fixed'
						,	'top': top+ 'px'
						,	'left': left+ 'px'
						});
						$contentDoc.find('a,button,input[type="button"],input[type="submit"]').click(function(){
							return false;
						});
					}).attr('src', '<?php echo $this->previewUrl?>');
					</script>
				</div>
			</div>
		</div>
	</section>
</div>
<div id="nbsFormPreviewUpdatingMsg">
	<?php _e('Loading preview...', NBS_LANG_CODE)?>
</div>
<div id="nbsFormGoToTop" class="nbsGoToTop">
	<a id="nbsFormGoToTopBtn" class="nbsGoToTopBtn" href="#">
		<img src="<?php echo uriNbs::_(NBS_IMG_PATH)?>pointer-up.png" /><br />
		<?php _e('Back to top', NBS_LANG_CODE)?>
	</a>
</div>
<div style="display: none;">
	<?php
		_WP_Editors::editor('ololo', 'lalala');
	?>
</div>
<?php dispatcherNbs::doAction('afterFormEdit', $this->form);?>
<div id="nbsSaveFormErrorWnd" style="display: none;">
	<div class="nbsSaveFormErrorEx" data-code="submit_btn">
		<?php _e('You have no submit fields in your Form - this make it\'s submission impossible. Please add one submit field in Fields tab - and then save your form.', NBS_LANG_CODE)?>
	</div>
	<div class="nbsSaveFormErrorEx" data-code="submit_data">
		<?php _e('You have no data to submit to in your form - in this case all form data will not be send anywhere. Go to Submit Options tab - and add there at least one data submit block - and then save your form.', NBS_LANG_CODE)?>
	</div>
</div>