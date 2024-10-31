var g_nbsMainMenu = null
,	g_nbsFileFrame = null	// File frame for wp media uploader
,	g_nbsEdit = true
,	g_nbsSortInProgress = false
,	g_nbsTopBarH = 93	// Height of the Top Editor Bar
,	g_nbsMainColorDark = '#09c9da'
,	g_nbsMainTollbar = null
,	g_nbsColorPickerOptions = {
		size: 2
	,	mode: 'hsv-h'
	,	noAlpha: true
};
jQuery(document).ready(function(){
	// Init custom js Twig functions
	_nbsExtendTwig();
	// Adding beforeStart event for sortable
	var oldMouseStart = jQuery.ui.sortable.prototype._mouseStart;
	jQuery.ui.sortable.prototype._mouseStart = function (event, overrideHandle, noActivation) {
		this._trigger("beforeStart", event, this._uiHash());
		oldMouseStart.apply(this, [event, overrideHandle, noActivation]);
	};
	jQuery('#nbsCanvas').on('click', 'a:not(.nbsEl)', function(event){
		event.preventDefault();
	});
	_nbsInitMainMenu();
	_nbsInitDraggable();
	_nbsInitMainToolbar();
	
	_nbsFitCanvasToScreen();
	jQuery(window).resize(function(){
		_nbsFitCanvasToScreen();
		_nbsGetMainToolbar().refresh();
	});
	jQuery('.nbsMainSaveBtn').click(function(){
		_nbsSaveCanvas();
		return false;
	});
	_nbsInitOctoDataChange();
	// Preview btn click
	jQuery('#nbsPreviewTplBtn').click(function(e){
		var $self = jQuery(this);
		_nbsSaveCanvas(function(){
			window.open( $self.attr('href') );
		});
		return false;
	});
});
jQuery(window).load(function(){
	jQuery('#nbsMainOctoForm').show();
	// Transform al custom chosen selects
	nbsInitCustomSelects();
	_nbsGetMainToolbar().refresh();
	window._octLoaded = true;
});
function _nbsInitMainToolbar() {
	g_nbsMainTollbar = new _nbsMainToolbar();
}
function _nbsGetMainToolbar() {
	return g_nbsMainTollbar;
}
function _nbsInitMainMenu() {
	var mainDelay = 100;
	jQuery('.nbsBlocksBar').slimScroll({
		height: jQuery(window).height() - g_nbsTopBarH
	,	railVisible: true
	,	alwaysVisible: true
	,	allowPageScroll: true
	,	position: 'right'
	,	color: g_nbsMainColorDark
	,	opacity: 1
	,	distance: 0
	,	borderRadius: '3px'
	,	wrapperPos: 'fixed'
	});
	jQuery('.nbsBlocksBar').each(function(){
		var classes = jQuery(this).attr('class');
		jQuery(this).attr('class', 'nbsBlockBarInner').parent().addClass(classes).attr('data-cid', jQuery(this).data('cid'));
	});
	jQuery('.nbsMainBar').slimScroll({
		height: jQuery(window).height() - g_nbsTopBarH
	,	railVisible: true
	,	alwaysVisible: true
	,	allowPageScroll: true
	,	color: g_nbsMainColorDark
	,	opacity: 1
	,	distance: 0
	,	borderRadius: '3px'
	,	width: jQuery('.nbsMainBar').width()
	,	wrapperPos: 'fixed'
	,	position: 'left'
	});
	jQuery('.nbsMainBar').each(function(){
		var classes = jQuery(this).attr('class');
		jQuery(this).attr('class', 'nbsMainBarInner').parent().addClass(classes);
	});
	g_nbsMainMenu = new nbsCategoriesMainMenu('.nbsMainBar');
	jQuery('.nbsBlocksBar').each(function(){
		g_nbsMainMenu.addSubMenu(this);
	});
	jQuery('.nbsMainBarHandle').click(function(){
		if(g_nbsMainMenu.isVisible()) {
			g_nbsMainMenu.checkHide();
		} else {
			g_nbsMainMenu.checkShow();
		}
		return false;
	});
	// For this plugin - it will be visible always by default
	g_nbsMainMenu.checkShow();
	jQuery('.nbsCatElement').mouseover(function(){
		var self = this;
		this._nbsMouseOver = true;
		var cid = jQuery(this).data('id');
		setTimeout(function(){
			if(self._nbsMouseOver)
				g_nbsMainMenu.showSubByCid( cid );
		}, mainDelay);
	}).mouseleave(function(e){
		this._nbsMouseOver = false;
		var cid = jQuery(this).data('id')
		,	movedTo = jQuery(e.relatedTarget)
		,	movedToBlockBar = false
		,	movedToCatBar = false;
		if(movedTo) {
			movedToBlockBar = movedTo.hasClass('nbsBlocksBar') || movedTo.parents('.nbsBlocksBar:first').length;
			if(!movedToBlockBar)	// Do not detect this each time - save processor time:)
				movedToCatBar = movedTo.hasClass('nbsCatElement') || movedTo.parents('.nbsMainBar').length;
		}
		if(movedTo && movedTo.length 
			&& (movedToBlockBar || movedToCatBar)
		) {
			return;
		}
		g_nbsMainMenu.hideSubByCid( cid );
	});
	jQuery('.nbsBlocksBar').mouseleave(function(e){
		var cid = jQuery(this).data('cid');
		g_nbsMainMenu.hideSubByCid( cid );
	});
}
function _nbsInitDraggable() {
	jQuery('#nbsCanvas').sortable({
		revert: true
	,	placeholder: 'ui-state-highlight'
	,	handle: '.nbsBlockMove'	// Use this setting to enable handler, or 2 setting above - to make sure it will not interupt other block/element clicking
	,	items: '.nbsBlock'
	,	start: function(event, ui) {
			//g_nbsBlockFabric.checkSortStart( ui );
		}
	,	stop: function(event, ui) {
			//g_nbsBlockFabric.checkSortStop( ui );
			_nbsSaveCanvasDelay( 400 );
		}
    });
    jQuery('.nbsBlocksList .nbsBlockElement').draggable({
		connectToSortable: '#nbsCanvas'
	,	helper: 'clone'
	,	revert: 'invalid'
	,	stop: function(event, ui) {
			if(!ui.helper.parents('#nbsCanvas').length) {	// Element dropped not in the canvas container
				ui.helper.remove();
				return;
			}
			g_nbsBlockFabric.addFromDrag( ui.helper, jQuery('#nbsCanvas').find('.ui-state-highlight') );
			//g_nbsMainMenu.checkHide();
		}
    });
    jQuery('.nbsBlocksList, .nbsBlocksList li').disableSelection();
}
function _nbsFitCanvasToScreen() {
	var canvasHeight = jQuery('#nbsCanvas').height()
	,	wndHeight = jQuery(window).height();
	if(canvasHeight < wndHeight) {
		jQuery('#nbsCanvas').height( wndHeight );
	}
}
function _nbsShowMainLoader() {
	jQuery('.nbsMainSaveBtn').width( jQuery('.nbsMainSaveBtn').width() );
	jQuery('.nbsMainSaveBtn').find('.nbsMainSaveBtnTxt').hide();
	jQuery('.nbsMainSaveBtn').find('.nbsMainSaveBtnLoader').show();
	jQuery('.nbsMainSaveBtn')
		.attr('disabled', 'disabled')
		.addClass('active');
	//jQuery('#nbsMainLoder').slideDown( g_nbsAnimationSpeed );
}
function _nbsHideMainLoader() {
	jQuery('.nbsMainSaveBtn').find('.nbsMainSaveBtnTxt').show();
	jQuery('.nbsMainSaveBtn').find('.nbsMainSaveBtnLoader').hide();
	jQuery('.nbsMainSaveBtn')
		.removeAttr('disabled')
		.removeClass('active');
	//jQuery('#nbsMainLoder').slideUp( g_nbsAnimationSpeed );
}
function _nbsSaveCanvasDelay(delay) {
	delay = delay ? delay : 200;
	setTimeout(_nbsSaveCanvas, delay);
}
function _nbsSaveCanvas(clb) {
	if(typeof(nbsOcto) === 'undefined' || !nbsOcto || !nbsOcto.id) {
		return;
	}
	_nbsShowMainLoader();
	var saveData = {
		id: nbsOcto.id
	,	blocks: g_nbsBlockFabric.getDataForSave()
	,	octo: jQuery('#nbsMainOctoForm').serializeAssoc()
	};
	jQuery.sendFormNbs({
		data: {mod: 'octo', action: 'save', data: saveData}
	,	onSuccess: function(res){
			_nbsHideMainLoader();
			if(!res.error) {
				if(res.data.id_sort_order_data) {
					var allBlocks = g_nbsBlockFabric.getBlocks();
					if(allBlocks.length) {
						for(var i = 0; i < res.data.id_sort_order_data.length; i++) {
							var sortOrderFind = parseInt(res.data.id_sort_order_data[ i ].sort_order);
							for(var j = 0; j < allBlocks.length; j++) {
								if(allBlocks[ j ].get('sort_order') == sortOrderFind && !allBlocks[ j ].get('id')) {
									allBlocks[ j ].set('id', parseInt(res.data.id_sort_order_data[ i ].id));
								}
							}
						}
					}
				}
				if(clb && typeof(clb) === 'function') {
					clb();
				}
			}
		}
	});
}
function _nbsSortInProgress() {
	return g_nbsSortInProgress;
}
function _nbsSetSortInProgress(state) {
	g_nbsSortInProgress = state;
}
function _nbsInitOctoDataChange() {
	// Transform all custom checkbox / radiobuttons in admin bar
	nbsInitCustomCheckRadio('#nbsMainOctoForm');
	// Label setting - should be as title for page
	jQuery('#nbsMainOctoForm [name="label"]').change(function(){
		jQuery('head title').html( jQuery(this).val() );
	});
	// Font setting
	jQuery('#nbsMainOctoForm [name="params[font_family]"]').change(function(){
		_nbsGetCanvas()._setFont( jQuery(this).val(), true );
	});
	// Width settings
	jQuery('#nbsMainOctoForm [name="params[width]"]').change(function(){
		_nbsGetCanvas().setWidth( jQuery(this).val() );
	});
	// Bg type switch
	jQuery('#nbsMainOctoForm [name="params[bg_type]"]').change(function(){
		if(!jQuery(this).prop('checked')) return;
		_nbsGetCanvas()._setBgType( jQuery(this).val() );
	});
	// Bg color input init
	_nbsCreateColorPickerOpt('.nbsOctoBgColor',  _nbsGetCanvas().getParam('bg_color'), function(container, color){
		_nbsGetCanvas()._updateFillColorFromColorpicker( color.tiny );
	});
	// Bg img selection
	jQuery('#nbsMainOctoForm .nbsOctoBgImgBtn').click(function(e){
		e.preventDefault();
		nbsCallWpMedia({
			clb: function(opts, attach, imgUrl) {
				__nbsSetCanvasBgImgOpt( attach.url );
			}
		});
	});
	// Bg img clear
	jQuery('#nbsMainOctoForm .nbsOctoBgImgRemove').click(function(e){
		e.preventDefault();
		__nbsSetCanvasBgImgOpt('');
	});
	// Bg img position options
	jQuery('#nbsMainOctoForm [name="params[bg_img_pos]"]').change(function(){
		_nbsGetCanvas()._setBgImgPos( jQuery(this).val() );
	});
	/*
	// Bg type switch
	jQuery('#nbsMainOctoForm [name="params[bg_type]"]').change(function(){
		if(!jQuery(this).prop('checked')) return;
		_nbsGetCanvas()._setBgType( jQuery(this).val() );
	});
	// Bg color input init
	_nbsCreateColorPickerOpt('.nbsOctoBgColor',  _nbsGetCanvas().getParam('bg_color'), function(container, color){
		_nbsGetCanvas()._updateFillColorFromColorpicker( color.tiny );
	});
	// Bg img selection
	jQuery('#nbsMainOctoForm .nbsOctoBgImgBtn').click(function(e){
		e.preventDefault();
		nbsCallWpMedia({
			clb: function(opts, attach, imgUrl) {
				__nbsSetCanvasBgImgOpt( attach.url );
			}
		});
	});
	// Bg img clear
	jQuery('#nbsMainOctoForm .nbsOctoBgImgRemove').click(function(e){
		e.preventDefault();
		__nbsSetCanvasBgImgOpt('');
	});
	// Bg img position options
	jQuery('#nbsMainOctoForm [name="params[bg_img_pos]"]').change(function(){
		_nbsGetCanvas()._setBgImgPos( jQuery(this).val() );
	});*/
	// Cover type switch
	jQuery('#nbsMainOctoForm [name="params[cover_type]"]').change(function(){
		if(!jQuery(this).prop('checked')) return;
		_nbsGetCanvas()._setCoverType( jQuery(this).val() );
	});
	// Cover color input init
	_nbsCreateColorPickerOpt('.nbsOctoCoverColor',  _nbsGetCanvas().getParam('cover_color'), function(container, color){
		_nbsGetCanvas()._updateCoverFillColorFromColorpicker( color.tiny );
	});
	// Cover img selection
	jQuery('#nbsMainOctoForm .nbsOctoCoverImgBtn').click(function(e){
		e.preventDefault();
		nbsCallWpMedia({
			clb: function(opts, attach, imgUrl) {
				__nbsSetCanvasCoverImgOpt( attach.url );
			}
		});
	});
	// Cover img clear
	jQuery('#nbsMainOctoForm .nbsOctoCoverImgRemove').click(function(e){
		e.preventDefault();
		__nbsSetCanvasCoverImgOpt('');
	});
	// Cover img position options
	jQuery('#nbsMainOctoForm [name="params[cover_img_pos]"]').change(function(){
		_nbsGetCanvas()._setCoverImgPos( jQuery(this).val() );
	});
	// Keywords meta tags manipulation
	jQuery('#nbsMainOctoForm [name="params[keywords]"]').change(function(){
		_nbsGetCanvas().setKeywords( jQuery(this).val() );
	});
	// Description meta tags manipulation
	jQuery('#nbsMainOctoForm [name="params[description]"]').change(function(){
		_nbsGetCanvas().setDescription( jQuery(this).val() );
	});
	// Disable grid snapping
	jQuery('#nbsMainOctoForm [name="nbs_dsbl_snap"]').change(function(){
		_nbsGetCanvas().setDsblSnap( jQuery(this).prop('checked') ? 1 : 0 );
	});
	// Reset template by default
	jQuery('#nbsMainOctoForm .nbsResetTplBtn').click(function(){
		if(confirm(toeLangNbs('Are you sure want to reset template by default? This will remove all your changes in this template.'))) {
			jQuery.sendFormNbs({
				btn: jQuery(this)
			,	data: {mod: 'octo', action: 'resetTpl', id: nbsOcto.id}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeReload();
					}
				}
			});
		}
		return false;
	});
	//
	jQuery('#nbsBackToAdminBtn').click(function(){
		_nbsSaveCanvas();
	});
}
function __nbsSetCanvasBgImgOpt(url) {
	_nbsGetCanvas()._setBgImg( url );
	jQuery('#nbsMainOctoForm .nbsOctoBgImg').attr('src', url ? url : jQuery('#nbsMainOctoForm .nbsOctoBgImg').data('noimg-url'));
	jQuery('#nbsMainOctoForm input[name="params[bg_img]"]').val( url );
	url 
		? jQuery('#nbsMainOctoForm .nbsOctoBgImgRemove').show()
		: jQuery('#nbsMainOctoForm .nbsOctoBgImgRemove').hide();
	setTimeout(function(){
		_nbsGetMainToolbar().refresh();	// Image canb have different size - so we need to check toolbar settings after this
	}, 500);
}
function __nbsSetCanvasCoverImgOpt(url) {
	_nbsGetCanvas()._setCoverImg( url );
	jQuery('#nbsMainOctoForm .nbsOctoCoverImg').attr('src', url ? url : jQuery('#nbsMainOctoForm .nbsOctoCoverImg').data('noimg-url'));
	jQuery('#nbsMainOctoForm input[name="params[cover_img]"]').val( url );
	url 
		? jQuery('#nbsMainOctoForm .nbsOctoCoverImgRemove').show()
		: jQuery('#nbsMainOctoForm .nbsOctoCoverImgRemove').hide();
	setTimeout(function(){
		_nbsGetMainToolbar().refresh();	// Image canb have different size - so we need to check toolbar settings after this
	}, 500);
}
function __nbsSetCanvasFavImgOpt(url) {
	_nbsGetCanvas()._setFavImg( url );
	jQuery('#nbsMainOctoForm .nbsOctoFavImg').attr('src', url ? url : jQuery('#nbsMainOctoForm .nbsOctoFavImg').data('noimg-url'));
	jQuery('#nbsMainOctoForm input[name="params[fav_img]"]').val( url );
	url 
		? jQuery('#nbsMainOctoForm .nbsOctoFavImgRemove').show()
		: jQuery('#nbsMainOctoForm .nbsOctoFavImgRemove').hide();
	setTimeout(function(){
		_nbsGetMainToolbar().refresh();	// Image canb have different size - so we need to check toolbar settings after this
	}, 500);
}
function _nbsAfterMaintDatesChange() {
	var progrElements = _nbsGetFabric().getElementsByCode('progress_bar');
	if(progrElements) {
		for(var i = 0; i < progrElements.length; i++) {
			progrElements[ i ].refreshProgress();
		}
	}
	var timerElements = _nbsGetFabric().getElementsByCode('timer');
	if(timerElements) {
		for(var i = 0; i < timerElements.length; i++) {
			timerElements[ i ].initFinishDate();
		}
	}
}
function _nbsCreateColorPickerOpt(selector, color, clb) {
    var $input = jQuery(selector).find('.nbsColorpickerInput'),
    	options = jQuery.extend({
    		convertCallback: function (colors) {
	    		var rgbaString = 'rgba(' + colors.webSmart.r + ',' + colors.webSmart.g + ',' + colors.webSmart.b + ',' + colors.alpha + ')';
	    		colors.tiny = new tinycolor( '#' + colors.HEX );
	    		colors.tiny.setAlpha( colors.alpha );
	    		colors.tiny.toRgbString = function () {
	    			return rgbaString;
	    		};

	    		if (clb) 
	    			clb($input, colors);

	    		$input.val(rgbaString);
	    	}
    	},
    	g_nbsColorPickerOptions
    );

    $input.css('background-color', color);

    $input.colorPicker(options);
}
function _nbsExtendTwig() {
	Twig.extendFunction('dynContent', function( block ){
		var blockObj = _nbsGetFabric().getByViewId( block.view_id );
		if(blockObj) {
			blockObj.updateDynContent();
		} else {
			console.log('CAN NOT FIND REQUIRED DYNAMIC BLOCK!!! VIEW ID - '+ block.view_id);
		}
	});
}