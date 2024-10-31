<div class="nbsFormOptRow">
	<label>
		<a target="_blank" href="<?php echo $this->promoLink?>" class="sup-promolink-input">
			<?php echo htmlNbs::checkbox('layered_style_promo', array(
				'checked' => 1,
				//'attrs' => 'disabled="disabled"',
			))?>
			<?php _e('Enable Layered Form Style', NBS_LANG_CODE)?>
		</a>
		<a target="_blank" class="button" style="margin-top: -8px;" href="<?php echo $this->promoLink?>"><?php _e('Available in PRO', NBS_LANG_CODE)?></a>
	</label>
	<div class="description"><?php _e('By default all Forms have modal style: it appears on user screen over the whole site. Layered style allows you to show your Form - on selected position: top, bottom, etc. and not over your site - but right near your content.', NBS_LANG_CODE)?></div>
</div>
<span>
	<div class="nbsFormOptRow">
		<span class="nbsOptLabel"><?php _e('Select position for your Form', NBS_LANG_CODE)?></span>
		<br style="clear: both;" />
		<div id="nbsLayeredSelectPosShell">
			<div class="nbsLayeredPosCell" style="width: 30%;" data-pos="top_left"><span class="nbsLayeredPosCellContent"><?php _e('Top Left', NBS_LANG_CODE)?></span></div>
			<div class="nbsLayeredPosCell" style="width: 40%;" data-pos="top"><span class="nbsLayeredPosCellContent"><?php _e('Top', NBS_LANG_CODE)?></span></div>
			<div class="nbsLayeredPosCell" style="width: 30%;" data-pos="top_right"><span class="nbsLayeredPosCellContent"><?php _e('Top Right', NBS_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
			<div class="nbsLayeredPosCell" style="width: 30%;" data-pos="center_left"><span class="nbsLayeredPosCellContent"><?php _e('Center Left', NBS_LANG_CODE)?></span></div>
			<div class="nbsLayeredPosCell" style="width: 40%;" data-pos="center"><span class="nbsLayeredPosCellContent"><?php _e('Center', NBS_LANG_CODE)?></span></div>
			<div class="nbsLayeredPosCell" style="width: 30%;" data-pos="center_right"><span class="nbsLayeredPosCellContent"><?php _e('Center Right', NBS_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
			<div class="nbsLayeredPosCell" style="width: 30%;" data-pos="bottom_left"><span class="nbsLayeredPosCellContent"><?php _e('Bottom Left', NBS_LANG_CODE)?></span></div>
			<div class="nbsLayeredPosCell" style="width: 40%;" data-pos="bottom"><span class="nbsLayeredPosCellContent"><?php _e('Bottom', NBS_LANG_CODE)?></span></div>
			<div class="nbsLayeredPosCell" style="width: 30%;" data-pos="bottom_right"><span class="nbsLayeredPosCellContent"><?php _e('Bottom Right', NBS_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
		</div>
		<?php echo htmlNbs::hidden('params[tpl][layered_pos]')?>
	</div>
</span>
<style type="text/css">
	#nbsLayeredSelectPosShell {
		max-width: 560px;
		height: 380px;
	}
	.nbsLayeredPosCell {
		float: left;
		cursor: pointer;
		height: 33.33%;
		text-align: center;
		vertical-align: middle;
		line-height: 110px;
	}
	.nbsLayeredPosCellContent {
		border: 1px solid #a5b6b2;
		margin: 5px;
		display: block;
		font-weight: bold;
		box-shadow: -3px -3px 6px #a5b6b2 inset;
		color: #739b92;
	}
	.nbsLayeredPosCellContent:hover, .nbsLayeredPosCell.active .nbsLayeredPosCellContent {
		background-color: #e7f5f6; /*rgba(165, 182, 178, 0.3);*/
		color: #00575d;
	}
</style>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var proExplainContent = jQuery('#nbsLayeredProExplainWnd').dialog({
			modal:    true
		,	autoOpen: false
		,	width: 460
		,	height: 180
		});
		jQuery('.nbsLayeredPosCell').click(function(){
			proExplainContent.dialog('open');
		});
	});
</script>
<!--PRO explanation Wnd-->
<div id="nbsLayeredProExplainWnd" style="display: none;" title="<?php _e('Improve Free version', NBS_LANG_CODE)?>">
	<p>
		<?php printf(__('This functionality and more - is available in PRO version. <a class="button button-primary" target="_blank" href="%s">Get it</a> today for 29$', NBS_LANG_CODE), $this->promoLink)?>
	</p>
</div>