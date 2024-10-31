<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="nbsSubscribersListsTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', NBS_LANG_CODE)?>">
					<button class="button" id="nbsSubscribersListsRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', NBS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', NBS_LANG_CODE)?>">
					<input id="nbsSubscribersListsTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', NBS_LANG_CODE)?>">
				</li>
				<li title="<?php _e('Add New List', NBS_LANG_CODE)?>">
					<a href="#" class="button nbsAddSubListBtn" id="nbsAddSubListBtn">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('Add List', NBS_LANG_CODE)?>
					</a>
				</li>
			</ul>
			<div id="nbsSubscribersListsTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="nbsSubscribersListsTbl"></table>
			<div id="nbsSubscribersListsTblNav"></div>
			<div id="nbsSubscribersListsTblEmptyMsg" style="display: none;">
				<h3><?php _e('You have no SubscribersListss for now. <a href="#" style="font-style: italic;" class="nbsAddSubListBtn">Create</a> your SubscribersLists!', NBS_LANG_CODE)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>
<div id="nbsAddSubListWnd" title="<?php _e('New Subscribers List', NBS_LANG_CODE)?>" style="display:none;">
	<form id="nbsAddSubListFrm" action="" method="post">
		<label>
			<?php _e('List Name', NBS_LANG_CODE)?>:
			<?php echo htmlNbs::text('label', array('required' => true))?>
		</label>
		<?php echo htmlNbs::hidden('mod', array('value' => 'subscribers_lists'))?>
		<?php echo htmlNbs::hidden('action', array('value' => 'create'))?>
		<div id="nbsAddSubListMsg"></div>
	</form>
</div>