<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="nbsNewsletterTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', NBS_LANG_CODE)?>">
					<button class="button" id="nbsNewsletterRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', NBS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', NBS_LANG_CODE)?>">
					<input id="nbsNewsletterTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', NBS_LANG_CODE)?>">
				</li>
			</ul>
			<div id="nbsNewsletterTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="nbsNewsletterTbl"></table>
			<div id="nbsNewsletterTblNav"></div>
			<div id="nbsNewsletterTblEmptyMsg" style="display: none;">
				<h3><?php printf(__('You have no Newsletters for now. <a href="%s" style="font-style: italic;">Create</a> your Newsletter!', NBS_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>
<div id="nbsEditSendingNewlsetterWnd" style="display: none;" title="<?php _e('Edit sending Newsletter', NBS_LANG_CODE)?>">
	<?php _e('You are trying to edit newsletter, that is in sending progress for now. If you sure want to edit it - then press "Ok", but newsletter will be paused, and you will need to restart it sending by clicking on "Send" button from edit screen.', NBS_LANG_CODE)?>
</div>