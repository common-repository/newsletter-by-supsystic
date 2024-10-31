<div id="nbsNewsletterEditTabs">
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
					<span class="nbsNewsletterTabTitle"><?php echo $tData['title']?></span>
				</a>
			<?php $i++; }?>
		</h3>
	</section>
	<section>
		<div class="supsystic-item supsystic-panel" style="padding-left: 10px;">
			<div id="containerWrapper">
				<form id="nbsNewsletterEditNewsletter">
					<?php foreach($this->tabs as $tKey => $tData) { ?>
						<div id="<?php echo $tKey?>" class="nbsTabContent">
							<?php echo $tData['content']?>
						</div>
					<?php }?>
					<?php if(isset($this->newsletter['params']['opts_attrs'])) {?>
						<?php foreach($this->newsletter['params']['opts_attrs'] as $optKey => $attr) {
							echo htmlNbs::hidden('params[opts_attrs]['. $optKey. ']', array('value' => $attr));
						}?>
					<?php }?>
					<?php echo htmlNbs::hidden('mod', array('value' => 'newsletters'))?>
					<?php echo htmlNbs::hidden('action', array('value' => 'save'))?>
					<?php echo htmlNbs::hidden('id', array('value' => $this->newsletter['id']))?>
					<?php echo htmlNbs::nonceForAction('save')?>
				</form>
				<div style="clear: both;"></div>
			</div>
		</div>
	</section>
</div>
<div id="nbsNewsletterPreview">
	<iframe id="nbsNewsletterPreviewFrame" width="" height="" frameborder="0" src="" style="width: 100%; height: 100%;"></iframe>
	<script type="text/javascript">
	jQuery('#nbsNewsletterPreviewFrame').on('load',function(){
		if(typeof(nbsHidePreviewUpdating) === 'function')
			nbsHidePreviewUpdating();
		var $this = jQuery(this)
		,	$contentDoc = $this.contents()
		//,	formShell = $contentDoc.find('.nbsNewsletterShell')
		,	paddingSize = 40
		,	newWidth = (jQuery(this).get(0).contentWindow.document.body.scrollWidth + paddingSize)
		,	newHeight = (jQuery(this).get(0).contentWindow.document.body.scrollHeight + paddingSize)
		,	$parent = jQuery('#nbsNewsletterPreview')
		,	zoomK = 0.3
		,	fullWidth = newWidth * zoomK;

		$this.width( newWidth ).height( newHeight );
		//$parent.width( newWidth * zoomK ).height( newHeight * zoomK ).css('float', 'left');
		$contentDoc.find('a,button,input[type="button"],input[type="submit"]').click(function(){
			return false;
		});
		//$this.zoom(zoomK, 'top left');
		/*jQuery('#nbsNewsletterEditTabs').css({
			'width': 'calc(100% - '+ fullWidth+ 'px)'
		});*/
		if(!$parent.find('.nbsEditTplOnPrevBtn').length) {
			$parent.append('<a href="<?php echo $this->editOctoUrl;?>" target="_blank" class="button nbsEditTplOnPrevBtn"><?php _e('Edit Template', NBS_LANG_CODE);?></a>');
		}
	}).attr('src', '<?php echo $this->previewUrl?>');
	</script>
</div>
<div id="nbsNewsletterPreviewUpdatingMsg">
	<?php _e('Loading preview...', NBS_LANG_CODE)?>
</div>
<div id="nbsNewsletterGoToTop" class="nbsGoToTop">
	<a id="nbsNewsletterGoToTopBtn" href="#" class="nbsGoToTopBtn">
		<img src="<?php echo uriNbs::_(NBS_IMG_PATH)?>pointer-up.png" /><br />
		<?php _e('Back to top', NBS_LANG_CODE)?>
	</a>
</div>
<?php dispatcherNbs::doAction('afterNewsletterEdit', $this->newsletter);?>
