<style type="text/css">
	.nbsAdminMainLeftSide {
		width: 56%;
		float: left;
	}
	.nbsAdminMainRightSide {
		width: <?php echo (empty($this->optsDisplayOnMainPage) ? 100 : 40)?>%;
		float: left;
		text-align: center;
	}
	#nbsMainOccupancy {
		box-shadow: none !important;
	}
</style>
<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<?php _e('Main page Go here!!!!', NBS_LANG_CODE)?>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>