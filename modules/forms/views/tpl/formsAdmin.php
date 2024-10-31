<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="nbsFormTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', NBS_LANG_CODE)?>">
					<button class="button" id="nbsFormRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', NBS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Create New Form', NBS_LANG_CODE)?>">
					<a href="<?php echo $this->addNewLink;?>" class="button">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('Add New Form', NBS_LANG_CODE)?>
					</a>
				</li>
				<li title="<?php _e('Search', NBS_LANG_CODE)?>">
					<input id="nbsFormTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', NBS_LANG_CODE)?>">
				</li>
			</ul>
			<div id="nbsFormTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="nbsFormTbl"></table>
			<div id="nbsFormTblNav"></div>
			<div id="nbsFormTblEmptyMsg" style="display: none;">
				<h3><?php printf(__('You have no Forms for now. <a href="%s" style="font-style: italic;">Create</a> your Form!', NBS_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>