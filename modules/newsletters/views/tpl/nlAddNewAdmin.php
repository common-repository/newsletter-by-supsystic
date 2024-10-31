<style type="text/css">
	.newsletters-list-item.sup-promo:after {
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
	<?php
		$haveUsersTpls = !empty($this->usersTplList);
	?>
	<div class="supsystic-item supsystic-panel">
		<form id="nbsCreateNewsletterFrm">
			<h3 style="line-height: 30px;">
				<?php if($this->changeFor) {
					printf(__('Change Template to any other from the list below or <a class="button" href="%s">return to Newsletter edit</a>', NBS_LANG_CODE), $this->editLink);
				} else {
					_e('Choose Newsletter Name, Lists and Template. You can change it later.', NBS_LANG_CODE);
				}?>
				<button class="button button-primary" style="margin-top: 1px;">
					<i class="fa fa-check"></i>
					<?php _e('Save', NBS_LANG_CODE)?>
				</button>
			</h3>
			<hr />
			<div id="containerWrapper">
				<?php if(!$this->changeFor) { ?>
					<div class="supsystic-bar supsystic-sticky sticky-padd-next sticky-save-width sticky-base-width-auto sticky-outer-height">
						<table class="form-table">
							<tr>
								<th><label for="nbsNewNewsletterLabel"><?php _e('Newsletter Name', NBS_LANG_CODE)?></label></th>
								<td>
									<?php echo htmlNbs::text('label', array(
										'attrs' => 'id="nbsNewNewsletterLabel" style="min-width: 250px;"', 
										'required' => true))?>
									<button class="button button-primary" style="margin-top: 1px;">
										<i class="fa fa-check"></i>
										<?php _e('Create Newsletter', NBS_LANG_CODE)?>
									</button>
								</td>
							</tr>
							<tr>
								<th><label for="nbsNewNewsletterLists"><?php _e('Newsletter Lists', NBS_LANG_CODE)?></label></th>
								<td>
									<?php echo htmlNbs::selectlist('slid', array(
										'options' => $this->listsForSelect,
										'attrs' => 'class="chosen" id="nbsNewNewsletterLists" data-placeholder="'. __('Select Lists', NBS_LANG_CODE). '"',
									))?>
								</td>
							</tr>
						</table>
						<?php echo htmlNbs::hidden('oid')?>
						<?php echo htmlNbs::hidden('mod', array('value' => 'newsletters'))?>
						<?php echo htmlNbs::hidden('action', array('value' => 'create'))?>
						<?php echo htmlNbs::nonceForAction('create')?>
						<div style="clear: both;"></div>
						<div id="nbsCreateNewsletterMsg"></div>
					</div>
				<?php } ?>
				<div class="newsletters-list">
					<?php if($haveUsersTpls) { ?>
						<div id="nbsTplsTabs">
							<h3 class="nav-tab-wrapper">
								<a class="nav-tab nav-tab-active" href="#nbsOriginalTplsTab"><?php _e('Original Templates', NBS_LANG_CODE)?></a>
								<a class="nav-tab" href="#nbsUsersTplsTab"><?php _e('Used Templates', NBS_LANG_CODE)?></a>
							</h3>
							<div id="nbsOriginalTplsTab" class="nbsTabContent">
					<?php }?>
					<?php foreach($this->list as $tpl) { ?>
						<?php $isPromo = isset($tpl['promo']) && !empty($tpl['promo']);?>
						<?php $promoClass = $isPromo ? 'sup-promo' : '';?>
						<div class="newsletters-list-item preset <?php echo $promoClass;?>" data-id="<?php echo ($isPromo ? 0 : $tpl['id'])?>">
							<?php if($isPromo) { ?>
                                <a href="<?php echo $tpl['promo_link']?>" target="_blank">
                                    <img src="<?php echo $tpl['img_preview_url']?>" class="nbsTplPrevImg" />
                                    <div class="preset-overlay">
                                        <h3>
                                            <span class="nbsTplLabel" style="color: #23282d;"><?php echo $tpl['label']?></span>
                                        </h3>
                                    </div>
                                </a>
                                <a href="<?php echo $tpl['promo_link']?>" target="_blank" class="button nbsPromoTplBtn isProTplBtn"><?php _e('Get in PRO', NBS_LANG_CODE)?></a>
							<?php } else {?>
                                <img src="<?php echo $tpl['img_preview_url']?>" class="nbsTplPrevImg" />
                                <div class="preset-overlay">
                                    <h3>
                                        <span class="nbsTplLabel" style="color: #23282d;"><?php echo $tpl['label']?></span>
                                    </h3>
                                </div>
                            <?php } ?>
						</div>
					<?php }?>
					<?php if($haveUsersTpls) { ?>
							</div>
							<div id="nbsUsersTplsTab" class="nbsTabContent">
								<?php foreach($this->usersTplList as $tpl) { ?>
								<div class="newsletters-list-item preset" data-id="<?php echo $tpl['id']?>">
									<img src="<?php echo $tpl['img_preview_url']?>" class="nbsTplPrevImg" />
									<div class="preset-overlay">
										<?php if(isset($tpl['newsletter_label'])) { ?>
											<h3>
												<span class="nbsTplLabel">
													<?php echo $tpl['newsletter_label']?>
												</span>
											</h3>
											<h5><?php echo $tpl['label']?></h5>
										<?php } else { ?>
											<h4>
												<span class="nbsTplLabel"><?php echo $tpl['label']?></span>
											</h4>
										<?php }?>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					<?php }?>
					<div style="clear: both;"></div>
				</div>
			</div>
		</form>
	</div>
</section>
<!--Change tpl wnd-->
<div id="nbsChangeTplWnd" title="<?php _e('Change Template', NBS_LANG_CODE)?>" style="display: none;">
	<form id="nbsChangeTplNewsletter">
		<?php _e('Are you sure you want to change your current template - to ', NBS_LANG_CODE)?><span id="nbsChangeTplNewLabel"></span>?
		<?php echo htmlNbs::hidden('id')?>
		<?php echo htmlNbs::hidden('new_tpl_id')?>
		<?php echo htmlNbs::hidden('mod', array('value' => 'newsletters'))?>
		<?php echo htmlNbs::hidden('action', array('value' => 'changeTpl'))?>
	</form>
	<div id="nbsChangeTplMsg"></div>
</div>
<!---->