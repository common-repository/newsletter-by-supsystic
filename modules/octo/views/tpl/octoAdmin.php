<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', NBS_LANG_CODE)?>">
					<button class="button" id="nbsPagesRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', NBS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Clear All')?>">
					<button class="button" id="nbsPagesClearBtn" disabled data-toolbar-button>
						<?php _e('Clear', NBS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', NBS_LANG_CODE)?>">
					<input id="nbsPagesTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', NBS_LANG_CODE)?>">
				</li>
			</ul>
			<div id="nbsPagesTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="nbsPagesTbl"></table>
			<div id="nbsPagesTblNav"></div>
			<div id="nbsPagesTblEmptyMsg" style="display: none;">
				<h3><?php _e('You have no Templates for now.', NBS_LANG_CODE)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>