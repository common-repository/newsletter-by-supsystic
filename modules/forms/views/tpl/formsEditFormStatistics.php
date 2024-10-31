<?php /*Total stats*/ ?>
<div class="nbsChartShell" data-chart="nbsFormTotalStats">
	<span class="nbsOptLabel">
		<?php _e('Total Statistics', NBS_LANG_CODE)?>
	</span>
	<hr />
	<div style="clear: both;"></div>
	<div id="nbsFormTotalStats" class="nbsChartArea"></div>
</div>
<div class="nbsNoStatsMsg" data-chart="nbsFormTotalStats">
	<?php _e('Total Statistics is empty for now.', NBS_LANG_CODE)?>
	<p class="description"><?php _e('Once your site visitors begin to use your form - all form statistics usage will be here.', NBS_LANG_CODE)?></p>
</div>
<?php echo $this->proStatsHtml;?>
<?php if(empty($this->proStatsHtml)) { ?>
	<a href="<?php echo frameNbs::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=forms_stats_graph&utm_campaign=newsletters')?>" target="_blank">
		<img style="max-width: 100%; height: auto;" src="<?php echo frameNbs::_()->getModule('supsystic_promo')->getModPath()?>img/forms-stats-promo.png" />
	</a>
<?php } else {
	echo $this->proStatsHtml;
}?>