<style type="text/css">
	.forms-list-item.sup-promo:after {
		background-image: url("<?php echo $this->getModule()->getAssetsUrl()?>img/assets/ribbon-2.png");
		background-repeat: no-repeat;
		background-position: 0;
		content: " ";
		position: absolute;
		display: block;
		
		top: 0;
		right: 0;
		width: 100px;
		height: 100px;
	}
</style>
<section>
	<div class="supsystic-item supsystic-panel">
		<h3 style="line-height: 30px;">
			<?php if($this->changeFor) {
				printf(__('Change Template to any other from the list below or <a class="button" href="%s">return to Form edit</a>', NBS_LANG_CODE), $this->editLink);
			} else {
				_e('Choose Form Template. You can change it later.', NBS_LANG_CODE);
			}?>
		</h3>
		<hr />
		<div id="containerWrapper" style="width: 90%; margin: 40px auto;">
			<?php if(!$this->changeFor) { ?>
				<div class="supsystic-bar supsystic-sticky sticky-padd-next sticky-save-width sticky-base-width-auto sticky-outer-height">
					<form id="nbsCreateFormFrm">
						<label>
							<h3 style="float: left; margin: 10px;"><?php _e('Form Name', NBS_LANG_CODE)?>:</h3>
							<?php echo htmlNbs::text('label', array('attrs' => 'style="float: left; width: 60%;"', 'required' => true))?>
						</label>
						<button class="button button-primary" style="margin-top: 1px;">
							<i class="fa fa-check"></i>
							<?php _e('Save', NBS_LANG_CODE)?>
						</button>
						<?php echo htmlNbs::hidden('original_id')?>
						<?php echo htmlNbs::hidden('mod', array('value' => 'forms'))?>
						<?php echo htmlNbs::hidden('action', array('value' => 'createFromTpl'))?>
					</form>
					<div style="clear: both;"></div>
					<div id="nbsCreateFormMsg"></div>
				</div>
			<?php } ?>
			<div  class="forms-list">
				<?php foreach($this->list as $forms) { ?>
					<?php $isPromo = isset($forms['promo']) && !empty($forms['promo']);?>
					<?php $promoClass = $isPromo ? 'sup-promo' : '';?>
					<div class="forms-list-item preset <?php echo $promoClass;?>" data-id="<?php echo ($isPromo ? 0 : $forms['id'])?>">
						<img src="<?php echo $forms['img_preview_url']?>" class="nbsTplPrevImg" />
						<div class="preset-overlay">
							<h3>
								<span class="nbsTplLabel"><?php echo $forms['label']?></span>
							</h3>
							<?php if($isPromo) { ?>
							<a href="<?php echo $forms['promo_link']?>" target="_blank" class="button nbsPromoTplBtn"><?php _e('Get in PRO', NBS_LANG_CODE)?></a>
							<?php }?>
						</div>
					</div>
				<?php }?>
				<div style="clear: both;"></div>
			</div>
		</div>
	</div>
</section>
<!--Change tpl wnd-->
<div id="nbsChangeTplWnd" title="<?php _e('Change Template', NBS_LANG_CODE)?>" style="display: none;">
	<form id="nbsChangeTplForm">
		<?php _e('Are you sure you want to change your current template - to ', NBS_LANG_CODE)?><span id="nbsChangeTplNewLabel"></span>?
		<?php echo htmlNbs::hidden('id')?>
		<?php echo htmlNbs::hidden('new_tpl_id')?>
		<?php echo htmlNbs::hidden('mod', array('value' => 'forms'))?>
		<?php echo htmlNbs::hidden('action', array('value' => 'changeTpl'))?>
	</form>
	<div id="nbsChangeTplMsg"></div>
</div>
<!---->