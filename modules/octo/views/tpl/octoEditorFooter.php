<?php
	global $wp_filter;
	//var_dump( $wp_filter['wp_print_footer_scripts'] ); exit();
	//do_action( 'admin_footer' );
	
	do_action( 'customize_controls_print_footer_scripts' );
?>
<style type="text/css">
	/*to leave some place at the top for admin bar. Add more space for editor menu*/
	#nbsCanvas {
		margin-top: 193px;
	}
	.nbsMainBar, .nbsBlocksBar {
		top: 93px;
	}
</style>
<!--Images selection button example-->
<div id="nbsChangeImgBtnExl" class="nbsChangeImgBtn">
	<div class="nbsChangeImgBtnTxt" style="">
		<?php _e('Select Image', NBS_LANG_CODE)?>
	</div>
	<i class="octo-icon octo-icon-lg icon-image nbsChangeImgBtnIcon"></i>
</div>
<!--Block menus example-->
<div id="nbsBlockMenuExl" class="nbsBlockMenu">
	<div class="nbsBlockMenuEl" data-menu="align">
		<div class="nbsBlockMenuElTitle nbsBlockMenuElAlignTitle">
			<?php _e('Content align', NBS_LANG_CODE)?>
		</div>
		<div class="nbsBlockMenuElAlignContent row">
			<div class="col-sm-4 nbsBlockMenuElElignBtn" data-align="left">
				<i class="octo-icon octo-icon-2x icon-aligne-left"></i>
			</div>
			<div class="col-sm-4 nbsBlockMenuElElignBtn" data-align="center">
				<i class="octo-icon octo-icon-2x icon-aligne-center"></i>
			</div>
			<div class="col-sm-4 nbsBlockMenuElElignBtn" data-align="right">
				<i class="octo-icon octo-icon-2x icon-aligne-right"></i>
			</div>
		</div>
		<?php echo htmlNbs::hidden('params[align]')?>
	</div>
	<div class="nbsBlockMenuEl" data-menu="add_slide">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-image nbsChangeImgBtnIcon"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Add Slide', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="add_gal_item">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-image nbsChangeImgBtnIcon"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Add Image', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="add_menu_item">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-plus-s"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Add Menu Item', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="edit_slides">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-manage nbsChangeImgBtnIcon"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Manage Slides', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="fill_color">
		<div class="nbsBlockMenuElAct">
			<?php echo htmlNbs::checkbox('params[fill_color_enb]')?>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Fill Color', NBS_LANG_CODE)?>
		</div>
		<div class="nbsBlockMenuElRightAct">
			<div class="nbsColorpickerInputShell">
				<?php echo htmlNbs::text('params[fill_color]', array(
					'attrs' => 'class="nbsColorpickerInput"'
				));?>
			</div>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="bg_img">
		<div class="nbsBlockMenuElAct">
			<?php echo htmlNbs::checkbox('params[bg_img_enb]')?>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Background Image...', NBS_LANG_CODE)?>
		</div>
		<div class="nbsBlockMenuElRightAct">
			<i class="octo-icon octo-icon-lg icon-image"></i>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="add_field">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-plus-s"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Add Field', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="sub_settings">
		<div class="nbsBlockMenuElAct">
			<i class="glyphicon glyphicon-send"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Subscribe Settings', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="add_grid_col">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-image"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Add Column', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="add_grid_row">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-image"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Add Row', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="paddings">
		<div class="nbsBlockMenuElAct">
			<i class="octo-icon octo-icon-lg icon-image"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Paddings', NBS_LANG_CODE)?>
		</div>
	</div>
	<div class="nbsBlockMenuEl" data-menu="dyn_content_sets">
		<div class="nbsBlockMenuElAct">
			<i class="fa fa-quote-left"></i>
		</div>
		<div class="nbsBlockMenuElTitle">
			<?php _e('Content Settings', NBS_LANG_CODE)?>
		</div>
	</div>
</div>
<!--Block toolbar example-->
<div id="nbsBlockToolbarEx" class="nbsBlockToolbar nbsToolbar">
	<div class="nbsToolItem nbsBlockSettings octo-icon icon-options"></div>
	<div class="nbsToolItem nbsBlockMove octo-icon icon-up-down"></div>
	<div class="nbsToolItem nbsBlockRemove octo-icon icon-trash"></div>
</div>
<!--Manage slides wnd-->
<div class="modal fade" id="nbsManageSlidesWnd" tabindex="-1" role="dialog" aria-labelledby="nbsManageSlidesWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="octo-icon octo-icon-2x icon-close-s" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('DRAG AND DROP SLIDES TO ORDER', NBS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body">
				<div class="nbsSlidesListPrev">
					
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="button nbsSlideManageAddBtn" style="float: left;">
					<i class="octo-icon octo-icon-lg icon-plus-s"></i>
					<?php _e('Add Slide', NBS_LANG_CODE)?>
				</a>
				<button type="button" class="button-primary nbsManageSlidesSaveBtn"><?php _e('Save', NBS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Manage slides - slide example-->
<div id="nbsSlideManageItemExl" class="nbsSlideManageItem">
	<div class="nbsSlideManageItemToolbar nbsToolbar">
		<div class="nbsToolItem nbsSlideManageItemRemove octo-icon icon-trash"></div>
	</div>
	<img src="" />
</div>
<!--Manage gallery item menu-->
<div id="nbsElMenuGalItemExl" class="nbsElMenu" style="min-width: 140px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsImgChangeBtn">
				<i class="glyphicon glyphicon-picture"></i>
				<?php _e('Select image', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsImgLinkBtn" data-sub-panel-show="link">
				<i class="glyphicon glyphicon-link"></i>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsImgMoveBtn">
				<i class="glyphicon glyphicon-move"></i>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="link">
			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('gal_item_link')?>
			</label>
			<label class="nbsElMenuSubPanelRow">
				<?php echo htmlNbs::checkbox('gal_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', NBS_LANG_CODE)?></span>
			</label>
		</div>
	</div>
</div>
<!--Image menu-->
<div id="nbsElMenuImgExl" class="nbsElMenu" style="min-width: 260px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsImgChangeBtn">
				<label>
					<?php echo htmlNbs::radiobutton('type', array('value' => 'img'))?>
					<?php _e('Select image', NBS_LANG_CODE)?>
					<i class="glyphicon glyphicon-picture"></i>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsImgVideoSetBtn" data-sub-panel-show="video">
				<label>
					<?php echo htmlNbs::radiobutton('type', array('value' => 'video'))?>
					<?php _e('Video', NBS_LANG_CODE)?>
					<i class="fa fa-video-camera"></i>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsLinkBtn" data-sub-panel-show="link">
				<label>
					<i class="glyphicon glyphicon-link"></i>
					<?php _e('Link', NBS_LANG_CODE)?>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="video">
			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('video_link')?>
			</label>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="link">
			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('icon_item_link')?>
			</label>
			<div style="display: none;" class="nbsPostLinkDisabled" data-postlink-to=":parent label [name='icon_item_link']"></div>

			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('icon_item_title')?>
			</label>
			<label class="nbsElMenuSubPanelRow">
				<?php echo htmlNbs::checkbox('icon_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', NBS_LANG_CODE)?></span>
			</label>
		</div>
	</div>
</div>
<!--Menu image menu-->
<div id="nbsElMenuMenuItemImgExl" class="nbsElMenu" style="min-width: 175px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsImgChangeBtn">
				<i class="glyphicon glyphicon-picture"></i>
				<?php _e('Select image', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
	</div>
</div>
<!--Add menu item wnd-->
<div class="modal fade" id="nbsAddMenuItemWnd" tabindex="-1" role="dialog" aria-labelledby="nbsAddMenuItemWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="octo-icon octo-icon-2x icon-close-s" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Menu Item Settings', NBS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body nbsElMenuSubPanel">
				<label class="nbsElMenuSubPanelRow">
					<span class="mce-input-name-txt"><?php _e('text', NBS_LANG_CODE)?></span>
					<?php echo htmlNbs::text('menu_item_text')?>
				</label>
				<label class="nbsElMenuSubPanelRow">
					<span class="mce-input-name-txt"><?php _e('link', NBS_LANG_CODE)?></span>
					<?php echo htmlNbs::text('menu_item_link')?>
				</label>
				<label class="nbsElMenuSubPanelRow">
					<?php echo htmlNbs::checkbox('menu_item_new_window')?>
					<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', NBS_LANG_CODE)?></span>
				</label>
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary nbsAddMenuItemSaveBtn"><?php _e('Save', NBS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Input menu-->
<div id="nbsElMenuInputExl" class="nbsElMenu" style="min-width: 175px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn">
				<label>
					<?php _e('Required', NBS_LANG_CODE)?>
					<?php echo htmlNbs::checkbox('input_required')?>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuMoveHandlerPlace"></div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
	</div>
</div>
<!--Input Button menu-->
<div id="nbsElMenuInputBtnExl" class="nbsElMenu" style="min-width: 30px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsImgMoveBtn">
				<i class="glyphicon glyphicon-move"></i>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
	</div>
</div>
<!--Standart Button menu-->
<div id="nbsElMenuBtnExl" class="nbsElMenu" style="min-width: 250px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsLinkBtn" data-sub-panel-show="link">
				<label>
					<i class="glyphicon glyphicon-link"></i>
					<?php _e('Link', NBS_LANG_CODE)?>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsColorBtn" data-sub-panel-show="color">
				<label>
					<?php _e('Color', NBS_LANG_CODE)?>
					<div class="nbsColorpickerInputShell">
						<?php echo htmlNbs::text('color', array(
							'attrs' => 'class="nbsColorpickerInput"'
						));?>
					</div>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="link">
			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('btn_item_link')?>
			</label>
			<div style="display: none;" class="nbsPostLinkDisabled" data-postlink-to=":parent label [name='btn_item_link']"></div>

			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('btn_item_title')?>
			</label>
			<label class="nbsElMenuSubPanelRow">
				<?php echo htmlNbs::checkbox('btn_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', NBS_LANG_CODE)?></span>
			</label>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="color"></div>
	</div>
</div>
<!--Grid Column menu-->
<div id="nbsElMenuGridColExl" class="nbsElMenu" style="min-width: 370px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn" style="">
				<?php echo htmlNbs::checkbox('enb_fill_color')?>
			</div>
			<div class="nbsElMenuBtn nbsColorBtn" title="<?php _e('Background Color', NBS_LANG_CODE)?>">
				<label>
					<?php _e('Bg Color', NBS_LANG_CODE)?>
					<div class="nbsColorpickerInputShell">
						<?php echo htmlNbs::text('color', array(
							'attrs' => 'class="nbsColorpickerInput"'
						));?>
					</div>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn" style="">
				<?php echo htmlNbs::checkbox('enb_bg_img')?>
			</div>
			<div class="nbsElMenuBtn nbsImgChangeBtn" title="<?php _e('Background Image', NBS_LANG_CODE)?>">
				<label>
					<?php _e('Bg Image', NBS_LANG_CODE)?>
					<i class="glyphicon glyphicon-picture"></i>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn" data-sub-panel-show="add_col">
				<i class="fa fa-plus"></i>
				<?php _e('Column', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn" data-sub-panel-show="add_row">
				<i class="fa fa-plus"></i>
				<?php _e('Row', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn" data-sub-panel-show="add_element">
				<i class="fa fa-plus"></i>
				<?php _e('Element', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn" data-sub-panel-show="merge_col">
				<i class="fa fa-compress"></i>
				<?php _e('Merge', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn" data-sub-panel-show="align">
				<i class="fa fa-align-center"></i>
				<?php _e('Align', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="add_col">
			<div class="nbsElMenuBtn nbsAddColBtn" data-to="left">
				<i class="fa fa-arrow-left"></i>
				<?php _e('To Left', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAddColBtn" data-to="right">
				<?php _e('To Right', NBS_LANG_CODE)?>
				<i class="fa fa-arrow-right"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="add_row">
			<div class="nbsElMenuBtn nbsAddRowBtn" data-to="top">
				<i class="fa fa-arrow-up"></i>
				<?php _e('To Top', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAddRowBtn" data-to="bottom">
				<?php _e('To Bottom', NBS_LANG_CODE)?>
				<i class="fa fa-arrow-down"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="merge_col">
			<div class="nbsElMenuBtn nbsMergeColBtn" data-to="left">
				<i class="fa fa-arrow-left"></i>
				<?php _e('Left', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsMergeColBtn" data-to="top">
				<i class="fa fa-arrow-up"></i>
				<?php _e('Top', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsMergeColBtn" data-to="bottom">
				<i class="fa fa-arrow-down"></i>
				<?php _e('Bottom', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsMergeColBtn" data-to="right">
				<?php _e('Right', NBS_LANG_CODE)?>
				<i class="fa fa-arrow-right"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="align">
			<?php _e('Horizontal', NBS_LANG_CODE)?>: 
			<div class="nbsElMenuBtn nbsAlignColBtn" data-to="left">
				<i class="fa fa-arrow-left"></i>
				<?php _e('Left', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAlignColBtn" data-to="center">
				<?php _e('Center', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAlignColBtn" data-to="right">
				<?php _e('Right', NBS_LANG_CODE)?>
				<i class="fa fa-arrow-right"></i>
			</div>
			<?php _e('Vertical', NBS_LANG_CODE)?>: 
			<div class="nbsElMenuBtn nbsAlignColBtn" data-to="top">
				<i class="fa fa-arrow-up"></i>
				<?php _e('Top', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAlignColBtn" data-to="middle">
				<?php _e('Center', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAlignColBtn" data-to="bottom">
				<?php _e('Bottom', NBS_LANG_CODE)?>
				<i class="fa fa-arrow-down"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="add_element">
			<div class="nbsElMenuBtn nbsAddElBtn" data-code="txt">
				<i class="fa fa-font"></i>
				<?php _e('Text', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAddElBtn" data-code="img">
				<i class="fa fa-picture-o"></i>
				<?php _e('Image', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtn nbsAddElBtn" data-code="btn">
				<i class="fa fa-hand-pointer-o"></i>
				<?php _e('Button', NBS_LANG_CODE)?>
			</div>
		</div>
	</div>
</div>
<!--Td menu-->
<div id="nbsElMenuTdExl" class="nbsElMenu" style="min-width: 30px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="color"></div>
	</div>
</div>
<!--Menu Icon menu:)-->
<div id="nbsElMenuIconExl" class="nbsElMenu" style="min-width: 414px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsIconLibBtn">
				<i class="fa fa-lg fa-pencil"></i>
				<?php _e('Change Icon', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn"  data-sub-panel-show="size">
				<i class="glyphicon glyphicons-resize-small"></i>
				<?php _e('Icon Size', NBS_LANG_CODE)?>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsColorBtn" data-sub-panel-show="color">
				<?php _e('Color', NBS_LANG_CODE)?>
				<div class="nbsColorpickerInputShell">
					<?php echo htmlNbs::text('color', array(
						'attrs' => 'class="nbsColorpickerInput"'
					));?>
				</div>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsLinkBtn" data-sub-panel-show="link">
				<label>
					<i class="glyphicon glyphicon-link"></i>
					<?php _e('Link', NBS_LANG_CODE)?>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel nbsElMenuSubPanelIconSize" data-sub-panel="size">
			<span data-size="fa-lg">lg</span>
			<span data-size="fa-2x">2x</span>
			<span data-size="fa-3x">3x</span>
			<span data-size="fa-4x">4x</span>
			<span data-size="fa-5x">5x</span>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="color"></div>
		<div class="nbsElMenuSubPanel" data-sub-panel="link">
			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('icon_item_link')?>
			</label>
			<div style="display: none;" class="nbsPostLinkDisabled" data-postlink-to=":parent label [name='icon_item_link']"></div>
			
			<label class="nbsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', NBS_LANG_CODE)?></span>
				<?php echo htmlNbs::text('icon_item_title')?>
			</label>
			<label class="nbsElMenuSubPanelRow">
				<?php echo htmlNbs::checkbox('icon_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', NBS_LANG_CODE)?></span>
			</label>
		</div>
	</div>
</div>
<!--Delimiter menu-->
<div id="nbsElMenuDelimiterExl" class="nbsElMenu" style="min-width: 370px;">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsColorBtn" data-sub-panel-show="color">
				<label>
					<?php _e('Color', NBS_LANG_CODE)?>
					<div class="nbsColorpickerInputShell">
						<?php echo htmlNbs::text('color', array(
							'attrs' => 'class="nbsColorpickerInput"'
						));?>
					</div>
				</label>
			</div>
			<div class="nbsElMenuBtnDelimiter"></div>
			<div class="nbsElMenuBtn nbsRemoveElBtn">
				<i class="glyphicon glyphicon-trash"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="color"></div>
	</div>
</div>
<!--Txt Dynamics menu-->
<div id="nbsElMenuDynTxtExl" class="nbsElMenu">
	<div class="nbsElMenuContent">
		<div class="nbsElMenuMainPanel">
			<div class="nbsElMenuBtn nbsColorBtn" data-for="color">
				<label>
					<?php _e('Color', NBS_LANG_CODE)?>
					<div class="nbsColorpickerInputShell">
						<?php echo htmlNbs::text('color', array(
							'attrs' => 'class="nbsColorpickerInput"'
						));?>
					</div>
				</label>
			</div>
			<div class="nbsElMenuBtn nbsColorBtn" data-for="background-color">
				<label>
					<?php _e('Background Color', NBS_LANG_CODE)?>
					<div class="nbsColorpickerInputShell">
						<?php echo htmlNbs::text('bg_color', array(
							'attrs' => 'class="nbsColorpickerInput nbsColorpickerBgInput"'
						));?>
					</div>
				</label>
			</div>
			<div class="nbsElMenuBtn nbsFontSizeBtn" data-sub-panel-show="font-size" data-for="font-size">
				Font Size
			</div>
			<div class="nbsElMenuBtn nbsFontBoldBtn" data-for="font-bold" title="<?php _e('Bold', NBS_LANG_CODE)?>">
				<i class="fa fa-bold"></i>
			</div>
			<div class="nbsElMenuBtn nbsFontItalicBtn" data-for="font-italic" title="<?php _e('Italic', NBS_LANG_CODE)?>">
				<i class="fa fa-italic"></i>
			</div>
		</div>
		<div class="nbsElMenuSubPanel" data-sub-panel="font-size">
			<?php echo htmlNbs::text('font_size')?>
		</div>
	</div>
</div>
<!--Add field wnd-->
<div class="modal fade" id="nbsAddFieldWnd" tabindex="-1" role="dialog" aria-labelledby="nbsAddFieldWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="octo-icon octo-icon-2x icon-close-s" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Field Settings', NBS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body nbsElMenuSubPanel">
				<label class="nbsElMenuSubPanelRow">
					<span class="mce-input-name-txt"><?php _e('name', NBS_LANG_CODE)?></span>
					<?php echo htmlNbs::text('new_field_name')?>
				</label>
				<label class="nbsElMenuSubPanelRow">
					<span class="mce-input-name-txt"><?php _e('label', NBS_LANG_CODE)?></span>
					<?php echo htmlNbs::text('new_field_label')?>
				</label>
				<label class="nbsElMenuSubPanelRow">
					<span class="mce-input-name-txt"><?php _e('type', NBS_LANG_CODE)?></span>
					<?php echo htmlNbs::selectbox('new_field_html', array('options' => array(
						'text' => __('Text', NBS_LANG_CODE),
						'email' => __('Email', NBS_LANG_CODE),
					)))?>
				</label>
				<label class="nbsElMenuSubPanelRow">
					<?php echo htmlNbs::checkbox('new_field_reuired')?>
					<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('required', NBS_LANG_CODE)?></span>
				</label>
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary nbsAddFieldSaveBtn"><?php _e('Save', NBS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Icons library wnd-->
<div class="modal fade" id="nbsIconsLibWnd" tabindex="-1" role="dialog" aria-labelledby="nbsIconsLibWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="octo-icon octo-icon-2x icon-close-s" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Icons Library', NBS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body nbsElMenuSubPanel">
				<div id="nbsSubSettingsWndTabs">
					<?php echo htmlNbs::text('icon_search', array(
						'attrs' => 'class="nbsIconsLibSearchTxt" placeholder="'. esc_html(__('Search, for example - pencil, music, ...', NBS_LANG_CODE)). '"',
					))?>
					<div class="nbsIconsLibList row"></div>
					<div class="nbsIconsLibEmptySearch alert alert-info" style="display: none;"><?php _e('Nothing found for <span class="nbsNothingFoundKeys"></span>, maybe try to search something else?', NBS_LANG_CODE)?></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary nbsIconsLibSaveBtn"><?php _e('Close', NBS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Dyn content settings wnd-->
<div class="modal fade" id="nbsDynContentSetsWnd" tabindex="-1" role="dialog" aria-labelledby="nbsDynContentSetsWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="octo-icon octo-icon-2x icon-close-s" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Dynamic content Settings', NBS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body nbsElMenuSubPanel">
				<div id="nbsSubSettingsWndTabs">
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Posts Count', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::number('posts_cnt')?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Posts Types', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::selectbox('posts_type', array(
							'options' => $this->postTypesForSelect,
						))?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Post title as link', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::checkbox('enb_title_link')?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Post image as link', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::checkbox('enb_img_link')?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('"Read More" button text', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::text('more_btn_txt')?>
					</label>
					
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Enable Title', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::checkbox('enb_title')?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Enable Image', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::checkbox('enb_img')?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Image max Width', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::number('img_width')?>
						<?php echo htmlNbs::selectbox('img_width_units', array(
							'options' => $this->imgWidthUnits,
						))?>
						<?php echo htmlNbs::span('nbsImgWidthUnitPx', array('value' => 'px')); ?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Enable Excerpt', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::checkbox('enb_excerpt')?>
					</label>
					<label class="nbsElMenuSubPanelRow">
						<span class="mce-input-name-txt"><?php _e('Enable "Read More" button', NBS_LANG_CODE)?></span>
						<?php echo htmlNbs::checkbox('enb_more_btn')?>
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary nbsDynContentLibSaveBtn"><?php _e('Apply', NBS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Elements library wnd-->
<?php /*?><div class="modal fade" id="nbsAddColElLibWnd" tabindex="-1" role="dialog" aria-labelledby="nbsAddColElWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="octo-icon octo-icon-2x icon-close-s" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Elements Library', NBS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body nbsElMenuSubPanel">
				<?php foreach($this->elementsForInsert as $elCode => $el) { ?>
					<a href="<?php echo $elCode;?>" class="nbsAddColElBtn button">
						<i class="fa <?php echo $el['icon'];?>"></i>
						<?php echo $el['label'];?>
					</a>
				<?php }?>
				<p class="nbsAddColElEx nbsEl" data-el="txt"><?php _e('Text go Here', NBS_LANG_CODE)?></p>
				<div class="nbsEl nbsElImg nbsElWithArea nbsAddColElEx" data-el="img">
					<div class="nbsElArea">
						<img class="nbsImg" src="https://supsystic-42d7.kxcdn.com/_assets/newsletters/img/blocks/ocean/2_icon2.png">
					</div>
				</div>
				<span class="nbsActBtn nbsEl nbsElInput nbsAddColElEx" data-el="btn" data-bgcolor="rgb(221, 68, 68)" style="background-color: rgb(221, 68, 68); border-radius: 5px; padding-left: 40px; padding-right: 40px; padding-top: 10px; padding-bottom: 10px;">
					<a style="color: #f9f9f9; text-decoration: none; " target="_blank" href="https://wordpress.org/plugins/newsletter-by-supsystic/" class="nbsEditArea nbsInputShell">Read More</a>
				</span>
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary nbsAddColElLibSaveBtn"><?php _e('Close', NBS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div><?php */?>
<!--Add elements examples-->
<div id="nbsAddElementsExamples">
	<p class="nbsAddColElEx nbsEl" data-el="txt"><?php _e('Text go Here', NBS_LANG_CODE)?></p>
	<div class="nbsEl nbsElImg nbsElWithArea nbsAddColElEx" data-el="img">
		<div class="nbsElArea">
			<img class="nbsImg" src="https://supsystic-42d7.kxcdn.com/_assets/newsletters/img/blocks/ocean/2_icon2.png">
		</div>
	</div>
	<span class="nbsActBtn nbsEl nbsElInput nbsAddColElEx" data-el="btn" data-bgcolor="rgb(221, 68, 68)" style="background-color: rgb(221, 68, 68); border-radius: 5px; padding-left: 40px; padding-right: 40px; padding-top: 10px; padding-bottom: 10px;">
		<a style="color: #f9f9f9; text-decoration: none; " target="_blank" href="https://wordpress.org/plugins/newsletter-by-supsystic/" class="nbsEditArea nbsInputShell">Read More</a>
	</span>
</div>
<!--Paddings library wnd-->
<div class="modal fade" id="nbsPaddingsWnd" tabindex="-1" role="dialog" aria-labelledby="nbsPaddingsWnd" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="octo-icon octo-icon-2x icon-close-s" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Block Paddings', NBS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body nbsElMenuSubPanel">
				<table width="100%">
					<?php $paddings = array(
						'top' => __('Top', NBS_LANG_CODE),
						'left' => __('Left', NBS_LANG_CODE),
						'right' => __('Right', NBS_LANG_CODE),
						'bottom' => __('Bottom', NBS_LANG_CODE),
					)?>
					<?php foreach($paddings as $padKey => $padLabel) { ?>
						<tr>
							<td>
								<label class="nbsElMenuSubPanelRow">
									<span class="mce-input-name-txt"><?php echo $padLabel;?></span>
									<?php echo htmlNbs::checkbox('enb_padding_'. $padKey)?>
								</label>
							</td>
							<td>
								<label class="nbsElMenuSubPanelRow">
									<?php echo htmlNbs::text('padding_'. $padKey)?>
									<?php if(in_array($padKey, array('left', 'right'))) {
										echo '%';
									} else {
										echo 'px';
									}?>
								</label>
							</td>
						</tr>
					<?php }?>
				</table>			
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary nbsPaddingsSaveBtn"><?php _e('Save', NBS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Movable handler-->
<div id="nbsMoveHandlerExl" class="nbsMoveHandler nbsShowSmooth">
	<i class="fa fa-arrows nbsOptIconBtn"></i>
</div>
<!--Remove row btn-->
<div id="nbsRemoveRowBtnExl" class="nbsRemoveRowBtn nbsShowSmooth nbsElMenuBtn">
	<i class="fa fa-trash-o nbsOptIconBtn"></i>
</div>
<div id="nbsColResizerHelper"></div>
<div id="nbsColResizerScreen">
	<div class="nbsResizeNumTarget"></div>
	<div class="nbsColResizeArrowRightCover">
		<div class="nbsColResizeArrowRight"></div>
	</div>
	<div class="nbsColResizeArrowLeftCover">
		<div class="nbsColResizeArrowLeft"></div>
	</div>
	<div class="nbsResizeNumRelated"></div>
</div>
<div id="nbsColResizer"></div>
<div id="nbsRowResizerHelper"></div>
<div id="nbsRowResizerScreen">
	<div class="nbsRowResizeArrowUpCover">
		<div class="nbsRowResizeArrowUp"></div>
	</div>
	<div class="nbsResizeNumTarget"></div>
</div>
<div id="nbsRowResizer"></div>
