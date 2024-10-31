<?php /*Common octo styles*/ ?>
<?php if(!$this->isSimple) {?>
	<!--Blocks loader-->
	<div id="nbsBlockLoader" class="nbsShowSmooth">
		<div class="nbsLoaderContent">
			<div class="nbsLoaderContentCell">
				<div class="nbsBlockLoaderIcon">
					<img class="glyphicon-spin" src="<?php echo $this->getModule()->getModPath()?>img/block-loader.png" />
				</div><br style="display: block; margin-top: 10px;" />
				<div class="nbsBlockLoaderTxt" data-base-txt="<?php _e('We are checking your data', NBS_LANG_CODE)?>">
					<?php _e('We are checking your data', NBS_LANG_CODE)?>
				</div>
			</div>
		</div>
	</div>
<?php }