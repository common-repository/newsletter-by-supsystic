<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="nbsSubscribersTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', NBS_LANG_CODE)?>">
					<button class="button" id="nbsSubscribersRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', NBS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', NBS_LANG_CODE)?>">
					<input id="nbsSubscribersTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', NBS_LANG_CODE)?>">
				</li>
				<li title="<?php _e('Search by List', NBS_LANG_CODE)?>">
					<?php echo htmlNbs::selectbox('tbl_search_list', array(
						'value' => 0,
						'options' => $this->listsForSelect,
						'attrs' => 'class="chosen" id="nbsSubscribersTblSearchList"',
					))?>
				</li>
				<li title="<?php _e('Import Subscribers', NBS_LANG_CODE)?>">
					<a href="<?php echo $this->importLink;?>" class="button">
						<i class="fa fa-fw fa-users"></i>
						<?php _e('Import', NBS_LANG_CODE)?>
					</a>
				</li>
				<li title="<?php _e('Add New Subscriber', NBS_LANG_CODE)?>">
					<a href="<?php echo $this->addNewLink;?>" class="button">
						<i class="fa fa-fw fa-user-plus"></i>
						<?php _e('Add', NBS_LANG_CODE)?>
					</a>
				</li>
			</ul>
			<div id="nbsSubscribersTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="nbsSubscribersTbl"></table>
			<div id="nbsSubscribersTblNav"></div>
			<div id="nbsSubscribersTblBulk" style="display: none;"></div>
			<div id="nbsSubscribersTblEmptyMsg" style="display: none;">
				<h3><?php printf(__('You have no Subscribers for now. <a href="%s" style="font-style: italic;">Create</a> your Subscribers!', NBS_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>