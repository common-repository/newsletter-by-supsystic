<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<div id="nbsSentPieStatsShell">
				<span class="nbsOptLabel">
					<?php _e('Sent Statistics', NBS_LANG_CODE)?>
				</span>
				<hr />
				<div id="nbsSentPieStats" class="nbsChartPieArea"></div>
			</div>
			<div id="nbsTotalStatsShell">
				<span class="nbsOptLabel">
					<?php _e('Total Statistics', NBS_LANG_CODE)?>
				</span>
				<hr />
				<div id="nbsTotalStats" class="nbsChartPieArea"></div>
			</div>
			<div id="nbsTotalStatsNoDataShell" style="display: none;"><?php _e('After your Newsletter will be sent at least one time - you will see here additional statistics - about opens and clicks.', NBS_LANG_CODE)?></div>
			<?php if(empty($this->proStatsHtml)) { ?>
				<a href="<?php echo frameNbs::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=stats_graph&utm_campaign=newsletters')?>" target="_blank">
					<img style="max-width: 100%; height: auto;" src="<?php echo frameNbs::_()->getModule('supsystic_promo')->getModPath()?>img/newsletter-stats-promo.png" />
				</a>
			<?php } else {
				echo $this->proStatsHtml;
			}?>
		</div>
	</div>
</section>