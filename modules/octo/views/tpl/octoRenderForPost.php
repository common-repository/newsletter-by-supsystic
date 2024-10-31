<?php
	$mainCanvasStylesArr = $mainCoverStyleArr = array();

	$mainCanvasStylesArr = $this->__nbsComputeBgStylesArr('bg');
	$mainCoverStyleArr = $this->__nbsComputeBgStylesArr('cover');
	
	if($this->zoom) {
		$mainCanvasStylesArr['-moz-transform'] = "scale($this->zoom)";
		$mainCanvasStylesArr['-o-transform'] = "scale($this->zoom)";
		$mainCanvasStylesArr['-webkit-transform'] = "scale($this->zoom)";
		$mainCanvasStylesArr['transform'] = "scale($this->zoom)";
		if($this->zoomOrigin) {
			$mainCanvasStylesArr['-moz-transform-origin'] = $this->zoomOrigin;
			$mainCanvasStylesArr['-o-transform-origin'] = $this->zoomOrigin;
			$mainCanvasStylesArr['-webkit-transform-origin'] = $this->zoomOrigin;
			$mainCanvasStylesArr['transform-origin'] = $this->zoomOrigin;
		}
	}
	$mainCanvasStylesArr['width'] = (isset($this->octo['params']['width']) ? $this->octo['params']['width'] : NBS_DEF_WIDTH)
		. (isset($this->octo['params']['width_units']) ? $this->octo['params']['width_units'] : NBS_DEF_WIDTH_UNITS);
	if(isset($this->octo['params']['font_family']) && !empty($this->octo['params']['font_family'])) {
		$mainCanvasStylesArr['font-family'] = $this->octo['params']['font_family'];
	}
	$nbsCanvasBackgroundAttr = "";
    $nbsCanvasIe9Gte = "";
	if(!empty($this->octo['params']['bg_img'])) {
		$mainCanvasStylesArr["background-color"] = 'transparent';
		$mainCoverStyleArr["background-color"] = 'transparent';
		$nbsCanvasBackgroundAttr = ' background="' . $this->octo['params']['bg_img'] . '"';
		$nbsCanvasIe9Gte = '<!--[if gte mso 9]>
            <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
                <v:fill type="tile" src="' . $this->octo['params']['bg_img'] .  '"/>
            </v:background>
            <![endif]-->';
    }
	$mainCanvasStylesStr = '';
	if(!empty($mainCanvasStylesArr)) {
		$mainCanvasStylesStr = utilsNbs::arrToCss($mainCanvasStylesArr);
	}
	$mainCoverStyleArr['width'] = '100%';
	$mainCanvasCoverStylesStr = '';
	if(!empty($mainCoverStyleArr)) {
		$mainCanvasCoverStylesStr = utilsNbs::arrToCss($mainCoverStyleArr);
	}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<title><?php echo $this->octo['label'];?></title>
	<?php if(!$this->isSimple) {?>
		<?php wp_enqueue_scripts(); ?>
		<?php wp_print_styles(); ?>
	<?php }?>
	<?php echo $this->stylesScriptsHtml;?>
	<style type="text/css">
		#nbsCanvas {
			<?php if(!empty($mainCanvasStylesStr)) {?>
			<?php echo $mainCanvasStylesStr?>
			<?php }?>
		}
	</style>
	<?php if(isset($this->octo['params']['fav_img']) && !empty($this->octo['params']['fav_img'])) { ?>
		<link rel="shortcut icon" href="<?php echo $this->octo['params']['fav_img'];?>" type="image/x-icon">
	<?php }?>
	<?php if(isset($this->octo['params']['keywords']) && !empty($this->octo['params']['keywords'])) { ?>
		<meta name="keywords" content="<?php echo htmlspecialchars($this->octo['params']['keywords']);?>">
	<?php }?>
	<?php if(isset($this->octo['params']['description']) && !empty($this->octo['params']['description'])) { ?>
		<meta name="description" content="<?php echo htmlspecialchars($this->octo['params']['description']);?>">
	<?php }?>
</head>
<body>
	<?php if($this->isEditMode) { ?>
		<div id="nbsMainLoder"></div>
		<div class="nbsMainBarHandle">
			<i class="octo-icon icon-blus-b"></i>
		</div>
		<form id="nbsMainOctoForm" style="display: none;">
			<div id="nbsMainTopBar" class="nbsMainTopBar supsystic-plugin">
				<div class="nbsMainTopBarLeft">
					<a id="nbsBackToAdminBtn" href="<?php echo $this->allPagesUrl?>" class="nbsMainTopBarBtn">
						<i class="octo-icon icon-back"></i>
						<?php _e('WP Admin', NBS_LANG_CODE)?>
					</a>
					<span class="nbsMainTopBarDelimiter">|</span>
				</div>
				<div class="nbsMainTopBarCenter">
					<?php /*?><div class="nbsMainOctoOpt">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Your Newsletter Subject.', NBS_LANG_CODE)?>"></i>
							<?php _e('Subject')?>
							<?php echo htmlNbs::text('label', array('value' => $this->octo['label']))?>
						</label>
					</div><?php */?>
					<div class="nbsMainOctoOpt">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Font family for your page. You can always change font for any text element using text editor tool.', NBS_LANG_CODE)?>"></i>
							<?php _e('Font', NBS_LANG_CODE)?>
							<?php echo htmlNbs::fontsList('params[font_family]', array(
								'value' => isset($this->octo['params']['font_family']) ? $this->octo['params']['font_family'] : '',
								'attrs' => 'id="nbsFontFamilySelect" class="chosen"',
								'standard' => true,
							))?>
						</label>
					</div>
					<div class="nbsMainOctoOpt" style="min-width: 150px;">
						<label style="float: left; margin-right: 5px;">
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Your tempalte width.', NBS_LANG_CODE)?>"></i>
							<?php _e('Width', NBS_LANG_CODE)?>
							<?php echo htmlNbs::number('params[width]', array(
								'value' => isset($this->octo['params']['width']) ? $this->octo['params']['width'] : NBS_DEF_WIDTH,
								'attrs' => 'style="width: 60px;"'
							))?>
						</label>
					</div>
					<div class="nbsMainOctoOpt">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Background color of your template. You can also set background for any block on page.', NBS_LANG_CODE)?>"></i>
							<?php _e('Background Color', NBS_LANG_CODE)?>
							<div class="nbsColorpickerInputShell nbsOctoBgColor">
								<?php echo htmlNbs::text('params[bg_color]', array(
									'attrs' => 'class="nbsColorpickerInput"',
									'value' => isset($this->octo['params']['bg_color']) ? $this->octo['params']['bg_color'] : '#fff',
								));?>
							</div>
						</label>
					</div>
					<div class="nbsMainOctoOpt nbsMainBgImgOptShell">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Set image as background. If it is set - it can overlap your Background Color option. Just click on the image from the right to change Background Image source.', NBS_LANG_CODE)?>"></i>
							<?php _e('Background Image', NBS_LANG_CODE)?>
							<?php
								$bgImgUrl = isset($this->octo['params']['bg_img']) ? $this->octo['params']['bg_img'] : '';
							?>
							<a class="nbsOctoBgImgBtn" href="#">
								<img class="nbsOctoBgImg" data-noimg-url="<?php echo $this->noImgUrl;?> "src="<?php echo $bgImgUrl ? $bgImgUrl : $this->noImgUrl;?>" />
							</a>
							<a 
								href="#" 
								class="nbsOctoBgImgRemove nbsMainTopBarBtn"
								<?php if(!$bgImgUrl) { ?>
									style="display: none;"
								<?php }?>
							>
								<i class="fa fa-times"></i>
							</a>
							<?php echo htmlNbs::hidden('params[bg_img]', array(
								'value' => $bgImgUrl,
							))?>
						</label>
					</div>
					<div class="nbsMainOctoOpt">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Background Image Position will define how we should show image on your background. Will work only if you will select Background Image.', NBS_LANG_CODE)?>"></i>
							<?php _e('Background Image Position', NBS_LANG_CODE)?>
							<?php echo htmlNbs::selectbox('params[bg_img_pos]', array(
								'options' => array('stretch' => __('Stretch', NBS_LANG_CODE), 'center' => __('Center', NBS_LANG_CODE), 'tile' => __('Tile', NBS_LANG_CODE)),
								'value' => isset($this->octo['params']['bg_img_pos']) ? $this->octo['params']['bg_img_pos'] : '',
								'attrs' => 'class="chosen" style="width: 100px;"',
							))?>
						</label>
					</div>
					<div class="nbsMainOctoOpt">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Cover color of your template.', NBS_LANG_CODE)?>"></i>
							<?php _e('Cover Color', NBS_LANG_CODE)?>
							<div class="nbsColorpickerInputShell nbsOctoCoverColor">
								<?php echo htmlNbs::text('params[cover_color]', array(
									'attrs' => 'class="nbsColorpickerInput"',
									'value' => isset($this->octo['params']['cover_color']) ? $this->octo['params']['cover_color'] : '#fff',
								));?>
							</div>
						</label>
					</div>
					<div class="nbsMainOctoOpt nbsMainCoverImgOptShell">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Set image as Cover. If it is set - it can overlap your Cover Color option. Just click on the image from the right to change Cover Image source.', NBS_LANG_CODE)?>"></i>
							<?php _e('Cover Image', NBS_LANG_CODE)?>
							<?php
								$coverImgUrl = isset($this->octo['params']['cover_img']) ? $this->octo['params']['cover_img'] : '';
							?>
							<a class="nbsOctoCoverImgBtn" href="#">
								<img class="nbsOctoCoverImg" data-noimg-url="<?php echo $this->noImgUrl;?> "src="<?php echo $coverImgUrl ? $coverImgUrl : $this->noImgUrl;?>" />
							</a>
							<a 
								href="#" 
								class="nbsOctoCoverImgRemove nbsMainTopBarBtn"
								<?php if(!$coverImgUrl) { ?>
									style="display: none;"
								<?php }?>
							>
								<i class="fa fa-times"></i>
							</a>
							<?php echo htmlNbs::hidden('params[cover_img]', array(
								'value' => $coverImgUrl,
							))?>
						</label>
					</div>
					<div class="nbsMainOctoOpt">
						<label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Cover Image Position will define how we should show image on your cover. Will work only if you will select Cover Image.', NBS_LANG_CODE)?>"></i>
							<?php _e('Cover Image Position', NBS_LANG_CODE)?>
							<?php echo htmlNbs::selectbox('params[cover_img_pos]', array(
								'options' => array('stretch' => __('Stretch', NBS_LANG_CODE), 'center' => __('Center', NBS_LANG_CODE), 'tile' => __('Tile', NBS_LANG_CODE)),
								'value' => isset($this->octo['params']['cover_img_pos']) ? $this->octo['params']['cover_img_pos'] : '',
								'attrs' => 'class="chosen" style="width: 100px;"',
							))?>
						</label>
					</div>
					<div class="nbsMainOctoOpt">
						<label>
							<?php $dsblSnap = (int) reqNbs::getVar('nbs_dsbl_snap', 'cookie'); ?>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Disable snapping when use Grid blocks resizing.', NBS_LANG_CODE)?>"></i>
							<?php _e('Disable Snap', NBS_LANG_CODE)?>
							<?php echo htmlNbs::checkbox('nbs_dsbl_snap', array(
								'checked' => $dsblSnap,
							))?>
						</label>
					</div>
					<div class="nbsMainOctoOpt">
						<a href="#" class="nbsResetTplBtn button button-primary">
							<i class="fa fa-retweet"></i>
							<?php _e('Reset Template', NBS_LANG_CODE)?>
						</a>
					</div>
					<div id="nbsMainOctoOptMore" class="nbsMainOctoOpt">
						<a href="#" id="nbsMainOctoOptMoreBtn" class="nbsMainTopBarBtn">
							<?php _e('More', NBS_LANG_CODE)?><br />
							<i class="fa fa-caret-down"></i>
						</a>
					</div>
				</div>
				<div class="nbsMainTopBarRight">
					<a id="nbsPreviewTplBtn" href="<?php echo uriNbs::_(array('baseUrl' => NBS_SITE_URL, 'tpl_preview' => $this->octo['id']));?>" target="_blank" class="nbsMainTopBarBtn nbsPreviewTplBtn"><?php _e('PREVIEW', NBS_LANG_CODE)?></a>
					<button class="button-primary nbsMainSaveBtn" data-txt="<?php _e('Save', NBS_LANG_CODE)?>">
						<div class="octo-icon octo-icon-2x icon-save-progress glyphicon-spin nbsMainSaveBtnLoader"></div>
						<span class="nbsMainSaveBtnTxt"><?php _e('Save', NBS_LANG_CODE)?></span>
					</button>
				</div>
			</div>
			<div id="nbsMainTopSubBar" class="nbsMainTopSubBar supsystic-plugin"></div>
		</form>
		<?php foreach($this->originalBlocksByMissions as $cat) { ?>
		<div class="navmenu navmenu-default navmenu-fixed-left offcanvas in canvas-slid nbsBlocksBar" data-cid="<?php echo $cat['id']?>">
			<ul class="nav navmenu-nav nbsBlocksList">
				<?php foreach($cat['blocks'] as $block) { ?>
					<li class="nbsBlockElement" data-id="<?php echo $block['id']?>">
						<img src="<?php echo $block['img_url']?>" class="nbsBlockElementImg" />
					</li>
				<?php }?>
			</ul>
		</div>
		<?php }?>
		<div class="navmenu navmenu-default navmenu-fixed-left offcanvas in canvas-slid nbsMainBar">
			<a target="_blank" href="https://supsystic.com/">
				<i class="fa fa-gear fa-4x nbsMainIcon"></i>
			</a>
			<ul class="nav navmenu-nav">
				<?php foreach($this->originalBlocksByMissions as $cat) { ?>
					<li class="nbsCatElement" data-id="<?php echo $cat['id']?>">
						<a href="#">
							<?php /*?><div class="nbsCatElementIcon" style="background-image: url(<?php echo $cat['icon_url']?>)"></div><?php */?>
							<?php echo $cat['label']?>
						</a>
					</li>
				<?php }?>
			</ul>
		</div>
		<script type="text/javascript">
			var g_nbsBlocksById = {};
			<?php foreach($this->originalBlocksByMissions as $cat) { ?>
				<?php foreach($cat['blocks'] as $block) { ?>
					g_nbsBlocksById[ <?php echo $block['id']?> ] = <?php echo utilsNbs::jsonEncode($block)?>;
				<?php }?>
			<?php }?>
		</script>
	<?php }?>
	<table id="nbsCanvasCover" width="<?php echo $mainCoverStyleArr['width'];?>" style="<?php echo $mainCanvasCoverStylesStr;?>" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<?php if(!$this->isEditMode) { ?>
					<center>
				<?php }?>
				<table id="nbsCanvas" width="<?php echo $mainCanvasStylesArr['width'];?>" style="<?php echo $mainCanvasStylesStr;?>" cellspacing="0" cellpadding="0" <?php echo $nbsCanvasBackgroundAttr;?>>
					<?php echo $nbsCanvasIe9Gte;?>
					<?php if(!empty($this->octo['blocks'])) {?>
						<?php foreach($this->octo['blocks'] as $block) { ?>
							<?php echo $block['rendered_html']; ?>
						<?php }?>
					<?php }?>
					<?php dispatcherNbs::doAction('templateEnd', $this->isEditMode);?>
				</table>
				<?php if(!$this->isEditMode) { ?>
					</center>
				<?php }?>
			</td>
		</tr>
	</table>
	<?php echo $this->commonFooter;?>
	<?php if($this->isEditMode) {
		echo $this->editorFooter;
	} else {
		echo $this->footer;
	}?>
</body>
</html>