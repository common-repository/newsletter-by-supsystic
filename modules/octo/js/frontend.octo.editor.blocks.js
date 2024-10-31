/**
 * Base block object - for extending
 * @param {object} blockData all block data from database (block database row)
 */
nbsBlockBase.prototype.destroy = function() {
	this._clearElements();
	this._$.slideUp(this._animationSpeed, jQuery.proxy(function(){
		this._$.remove();
		g_nbsBlockFabric.removeBlockByIter( this.getIter() );
		_nbsSaveCanvas();
	}, this));
};
nbsBlockBase.prototype.build = function(params) {
	params = params || {};
	var innerHtmlContent = '';
	if(this._data.html && this._data.html != '') {
		innerHtmlContent += '<td class="nbsBlockContent">'+ this._data.html+ '</td>';
	}
	innerHtmlContent += '<td class="nbsBlockMenuShell" valign="top"></td>';
	innerHtmlContent = '<tr class="nbsBlock" id="{{block.view_id}}">'+ innerHtmlContent+ '</tr>';
	if(this._data.css && this._data.css != '') {
		innerHtmlContent += '<style type="text/css" class="nbsBlockStyle">'+ this._data.css+ '</style>';
	}
	if(!this._data.session_id) {
		this._data.session_id = mtRand(1, 999999);
	}
	if(!this._data.view_id) {
		this._data.view_id = 'nbsBlock_'+ this._data.session_id;
	}
	var template = twig({
		data: innerHtmlContent
	});
	var generatedHtml = template.render({
		block: this._data
	});
	this._$ = jQuery(generatedHtml);
	if(params.insertAfter) {
		this._$.insertAfter( params.insertAfter );
	}
	this._initElements();
	this._initHtml();
};
nbsBlockBase.prototype.set = function(key, value) {
	this._data[ key ] = value;
};
nbsBlockBase.prototype.setData = function(data) {
	this._data = data;
};
nbsBlockBase.prototype.getData = function() {
	return this._data;
};
nbsBlockBase.prototype.appendToCanvas = function() {
	this._$.appendTo('#nbsCanvas');
};
nbsBlockBase.prototype.getElementByIterNum = function(iterNum) {
	return this._elements[ iterNum ];
};
nbsBlockBase.prototype.getElementByHtml = function($element) {
	return this._elements[ $element.data('iter-num') ];
};
nbsBlockBase.prototype.removeElementByIterNum = function(iterNum) {
	this._elements.splice( iterNum, 1 );
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].setIterNum( i );
		}
	}
};
nbsBlockBase.prototype.destroyElementByIterNum = function(iterNum, clb) {
	this.getElementByIterNum( iterNum ).destroy( clb );	// It will call removeElementByIterNum() inside element destroy method
};
nbsBlockBase.prototype._initHtml = function() {
	this._beforeInitHtml();
	this._initMenuHtml();
	this._buildMenu();
};
nbsBlockBase.prototype._initMenuHtml = function() {
	this._$.find('.nbsBlockMenuShell').append( jQuery('#nbsBlockToolbarEx').clone().removeAttr('id') );
	this._$.find('.nbsBlockRemove').click(jQuery.proxy(function(){
		if(confirm(toeLangNbs('Are you sure want to delete this block?'))) {
			this.destroy();
		}
		return false;
	}, this));
	this._$.find('.nbsBlockSettings').click(jQuery.proxy(function(event){
		jQuery('#'+ this._$.attr('id')).contextMenu({
			x: event.pageX - 100
		,	y: event.pageY
		});
	}, this));
};
nbsBlockBase.prototype._beforeInitHtml = function() {
	
};
nbsBlockBase.prototype._rebuildCss = function() {
	var template = twig({
		data: this._data.css
	});
	var generatedHtml = template.render({
		block: this._data
	});
	this.getStyle().html( generatedHtml );
};
nbsBlockBase.prototype.getStyle = function() {
	return this._$.find('style.nbsBlockStyle');
};
nbsBlockBase.prototype.setTaggedStyle = function(style, tag, elData) {
	this.removeTaggedStyle( tag );
	var $style = this.getStyle()
	,	styleHtml = $style.html()
	,	tags = this._getTaggedStyleStartEnd( tag );
	
	var template = twig({
		data: style
	});
	var generatedStyle = template.render({
		el: elData
	,	table: this._data
	}),	fullGeneratedStyleTag = tags.start+ "\n"+ generatedStyle+ "\n"+ tags.end;
	$style.html(styleHtml+ fullGeneratedStyleTag);
	this.set('css', this.get('css')+ this._revertReplaceContent(fullGeneratedStyleTag));
};
nbsBlockBase.prototype.removeTaggedStyle = function(tag, params) {
	params = params || {};
	var tags = this._getTaggedStyleStartEnd(tag, true)
	,	$style = params.$style ? params.$style : this.getStyle()
	,	styleHtml = params.styleHtml ? params.styleHtml : $style.html()
	,	replaceRegExp = new RegExp(tags.start+ '(.|[\n\r])+'+ tags.end, 'gmi');
	$style.html( styleHtml.replace(replaceRegExp, '') );
	this.set('css', this.get('css').replace(replaceRegExp, ''));
};
nbsBlockBase.prototype.getTaggedStyle = function(tag) {
	// TODO: Finish this method
	var tags = typeof(tag) === 'string' ? this._getTaggedStyleStartEnd(tag) : tag;
};
nbsBlockBase.prototype._getTaggedStyleStartEnd = function(tag, forRegExp) {
	return {
		start: forRegExp ? '\\/\\*start for '+ tag+ '\\*\\/' : '/*start for '+ tag+ '*/'
	,	end: forRegExp ? '\\/\\*end for '+ tag+ '\\*\\/' : '/*end for '+ tag+ '*/'
	};
};
nbsBlockBase.prototype._initMenuItem = function(newMenuItemHtml, item) {
	if(this['_initMenuItem_'+ item.type] && typeof(this['_initMenuItem_'+ item.type]) === 'function') {
		var menuItemName = this.getParam('menu_item_name_'+ item.type);
		if(menuItemName && menuItemName != '') {
			newMenuItemHtml.find('.nbsBlockMenuElTitle').html( menuItemName );
		}
		this['_initMenuItem_'+ item.type]( newMenuItemHtml, item );
	}
};
nbsBlockBase.prototype._initMenuItem_align = function(newMenuItemHtml, item) {
	if(this._data.params && this._data.params.align) {
		//newMenuItemHtml.find('input[name="params[align]"]').val( this._data.params.align.val );
		//newMenuItemHtml.find('.nbsBlockMenuElElignBtn').removeClass('active');
		//newMenuItemHtml.find('.nbsBlockMenuElElignBtn[data-align="'+ this._data.params.align.val+ '"]').addClass('active');
		this._setAlign( this._data.params.align.val, true, newMenuItemHtml );
	}
	var self = this;
	newMenuItemHtml.find('.nbsBlockMenuElElignBtn').click(function(){
		self._setAlign( jQuery(this).data('align') );
	});
};
nbsBlockBase.prototype._clickMenuItem_align = function(options) {
	return false;
};
nbsBlockBase.prototype.getMainTbl = function() {
	return this.getContent().find('.nbsMainTbl');
};
nbsBlockBase.prototype._setAlign = function( align, ignoreAutoSave, menuItemHtml ) {
	var $mainContTbl = this.getMainTbl();
	$mainContTbl.attr('align', align).css('text-align', align);

	this.setParam('align', align);

	if(!menuItemHtml) {
		var menuOpt = this._$.data('_contentMenuOpt');
		menuItemHtml = menuOpt.items.align.$node;
	}
	menuItemHtml.find('input[name="params[align]"]').val( align );
	menuItemHtml.find('.nbsBlockMenuElElignBtn').removeClass('active');
	menuItemHtml.find('.nbsBlockMenuElElignBtn[data-align="'+ align+ '"]').addClass('active');

	if(!ignoreAutoSave) {
		_nbsSaveCanvas();
	}
};
// For now fill color used only in slider, but we assume that it can be used in other block types too - so let it be in base block type for now.
// But if it will be only for slider block type for a long type - you can move it to slider block class - OOP is really good for us:)
nbsBlockBase.prototype._initMenuItem_fill_color = function(newMenuItemHtml, item) {
	var self = this;
	
	if(this._data.params && this._data.params.fill_color_enb && parseInt(this._data.params.fill_color_enb.val)) {
		newMenuItemHtml.find('input[name="params[fill_color_enb]"]').attr('checked', 'checked');
		this._updateFillColor( true );
	}
	
	newMenuItemHtml.find('input[name="params[fill_color_enb]"]').change(function(){
		self.setParam('fill_color_enb', jQuery(this).prop('checked') ? 1 : 0);
		self._updateFillColor();
	});
	
	var initColor = new tinycolor( self.getParam('fill_color') );
	
	var $input = newMenuItemHtml.find('.nbsColorpickerInput'),
		options = jQuery.extend({
			convertCallback: function (colors) {
	    		var rgbString = 'rgb('+ colors.webSmart.r+ ','+ colors.webSmart.g+ ','+ colors.webSmart.b+ ')';
	    		var tiny = new tinycolor( rgbString, 'rgb' );

	    		self._updateFillColorFromColorpicker(tiny, true);

	    		$input.attr('value', rgbString);
	    	}
		},
		g_nbsColorPickerOptions
	);
	
	$input.css('background-color', initColor.toHexString());
    $input.attr('value', initColor.toHexString());
    $input.colorPicker(options);
};
nbsBlockBase.prototype._updateFillColorFromColorpicker = function( color, ignoreAutoSave ) {
	this.setParam('fill_color', color.toHexString());
	this._updateFillColor( ignoreAutoSave );
};
nbsBlockBase.prototype._updateFillColor = function( ignoreAutoSave ) {
	var fillColorEnb = parseInt(this.getParam('fill_color_enb'))
	,	$overlay = this.getMainTbl()
	,	overlayUsed = false	// No overlays for now
	,	bgImgEnb = parseInt(this.getParam('bg_img_enb'));
	if(!overlayUsed) {
		var fillColorSelector = this.getParam('fill_color_selector');
		$overlay = fillColorSelector ? this._$.find( fillColorSelector ) : $overlay;
	}
	if(fillColorEnb) {
		var fillColor = this.getParam('fill_color');
		$overlay.removeAttr('background').attr({
			'bgcolor': fillColor
		}).css({
			'background-color': fillColor
		});
		// Disable bg img when bg color - enabled
		if(bgImgEnb) {
			this.setParam('bg_img_enb', 0);
			if(this._$menuItems['bg_img']) {
				var $bgImgEnbCheck = this._$menuItems['bg_img'].find('input[name="params[bg_img_enb]"]');
				$bgImgEnbCheck.removeProp('checked');
				nbsCheckUpdate( $bgImgEnbCheck );
				$overlay.css({
					'background-image': 'none'
				});
			}
		}
	} else {
		if(overlayUsed)
			$overlay.hide();
		if(!bgImgEnb) {
			this._clearBg();
		}
	}
	if(!ignoreAutoSave) {
		_nbsSaveCanvas();
	}
};
nbsBlockBase.prototype._clickMenuItem_fill_color = function(options) {
	// Show color-picker on menu item click
	options.items.fill_color.$node.find('.nbsColorpickerInput').trigger('focus');
	return false;
};
nbsBlockBase.prototype._initMenuItem_bg_img = function(newMenuItemHtml, item) {
	if(this._data.params && this._data.params.bg_img_enb && parseInt(this._data.params.bg_img_enb.val)) {
		newMenuItemHtml.find('input[name="params[bg_img_enb]"]').attr('checked', 'checked');
	}
	var self = this;
	newMenuItemHtml.find('input[name="params[bg_img_enb]"]').change(function(){
		var bgImgEnb = jQuery(this).prop('checked') ? 1 : 0;
		self.setParam('bg_img_enb', bgImgEnb);
		// Disable fill color when bg color - enabled
		var fillColorEnb = parseInt(self.getParam('fill_color_enb'));
		if(bgImgEnb) {
			self.getMainTbl().css({
				'background-image': ''
			});
			if(fillColorEnb) {
				self.setParam('fill_color_enb', 0);
				var $fillColorEnbCheck = self._$menuItems['fill_color'].find('input[name="params[fill_color_enb]"]');
				$fillColorEnbCheck.removeProp('checked');
				nbsCheckUpdate( $fillColorEnbCheck );
			}
		} else {
			if(!fillColorEnb) {
				self._clearBg();
			}
		}
		self._updateBgImg();
	});
};
nbsBlockBase.prototype._clearBg = function() {
	this.getMainTbl().css({
		'background-color': 'transparent'
	});
};
nbsBlockBase.prototype._clickMenuItem_bg_img = function(options) {
	var self = this;
	nbsCallWpMedia({
		id: this._$.attr('id')
	,	clb: function(opts, attach, imgUrl) {
			// we will use full image url from attach.url always here (not image with selected size imgUrl) - as this is bg image
			// but if you see really big issue with this - just try to do it better - but don't broke everything:)
			self.setParam('bg_img', attach.url);
			self._updateBgImg();
		}
	});
};
nbsBlockBase.prototype._updateBgImg = function( ignoreAutoSave ) {
	var $mainTbl = this.getMainTbl()
	,	bgImbEnb = parseInt( this.getParam('bg_img_enb') );
	if(bgImbEnb) {
		var bgImg = this.getParam('bg_img');
		if(bgImg) {
			$mainTbl.removeAttr('bgcolor').attr('background', bgImg).css({
				'background-color': 'transparent'
			,	'background-image': 'url('+ bgImg+ ')'
			});
		}
	} else {
		$mainTbl.removeAttr('background', bgImg).css({
			'background-image': ''
		});
	}
	if(!ignoreAutoSave) {
		_nbsSaveCanvas();
	}
};
nbsBlockBase.prototype._clickMenuItem = function(key, options) {
	if(this['_clickMenuItem_'+ key] && typeof(this['_clickMenuItem_'+ key]) === 'function') {
		return this['_clickMenuItem_'+ key]( options );
	}
};
nbsBlockBase.prototype._buildMenu = function() {
	if(this._data.params && this._data.params.menu_items && this._data.params.menu_items.val != '') {
		var itemKeys = this._data.params.menu_items.val.split('|')
		,	menuItems = {}
		,	self = this;
		for(var i = 0; i < itemKeys.length; i++) {
			menuItems[ itemKeys[i] ] = {
				type: itemKeys[i]
			,	callback: function(key, options) {
					return self._clickMenuItem( key, options );
				}
			,	iterNum: i
			};
			jQuery.contextMenu.types[ itemKeys[i] ] = function(item, opt, root) {
				var $html = jQuery('#nbsBlockMenuExl').find('.nbsBlockMenuEl[data-menu="'+ item.type+ '"]');
				if($html && $html.length) {
					var $newMenuItemHtml = $html.clone();
					$newMenuItemHtml.appendTo(this);
					// We can't use i here - as this is callback for earlier call, so use here our defined param iterNum
					if(item.iterNum < itemKeys.length - 1)	{	// Visual delimiter for all menu items except last one
						jQuery('<div class="nbsBlockMenuDelim" />').appendTo(this);
					}
					self._initMenuItem( $newMenuItemHtml, item );
					self._$menuItems[ item.type ] = $newMenuItemHtml;
				} else {
					console.log('Can not Find Element Menu Item: '+ item.type+ ' !!!');
				}
			};
		}
		var menuOnbs = {
			selector: '#'+ this._$.attr('id')
		,	zIndex: 9999
		,	position: function(opt, x, y) {
				if(!opt._nbsCustInpInited) {
					nbsInitCustomCheckRadio( opt.$menu );
					opt._nbsCustInpInited = true;
				}
				opt.$menu.css({top: y, left: x - opt.$menu.width() / 2});
			}
		,	items: menuItems
		};
		jQuery.contextMenu( menuOnbs );
	}
};
nbsBlockBase.prototype.getContent = function() {
	return this._$.find('.nbsBlockContent:first');
};

/**
 * returns dynamic block content with static part changed by editor and original dynamic part
 */
nbsBlockBase.prototype.getStaticContent = function() {
	var fullContent = jQuery('<div>').append(jQuery(this.getContent().html()));
	var originalStaticPart = this.get('html');
	var dynTagContent = jQuery(originalStaticPart).find(".nbsDynArea:first");
	fullContent.find(".nbsDynArea:first").replaceWith(dynTagContent);
	return fullContent;
};
nbsBlockBase.prototype._revertReplaceContent = function(content) {
	var revertReplace = [
		{key: 'view_id'}
	];
	for(var i = 0; i < revertReplace.length; i++) {
		var key = revertReplace[ i ].key
		,	value = this.get( key )
		,	replaceFrom = [ value ]
		,	replaceTo = revertReplace[i].raw ? '{{table.'+ key+ '|raw}}' : '{{table.'+ key+ '}}';
		if(typeof(value) === 'string' && revertReplace[i].raw) {
			replaceFrom.push( value.replace(/\s+\/>/g, '>') );
		}
		for(var j = 0; j < replaceFrom.length; j++) {
			content = str_replace(content, replaceFrom[ j ], replaceTo);
		}
	}
	return content;
};
nbsBlockBase.prototype.getHtml = function() {
	var html = '';
	if(parseInt(this.getParam('dyn_content'))) {
		//html = this.get('html');
		html = this.getStaticContent().html(); // this used for storing static html pf dynamic block
	} else {
		html = this.getContent().html();
	}
	return this._revertReplaceContent( html );
};
nbsBlockBase.prototype.getCss = function() {
	var css = this.getStyle().html();
	return this._revertReplaceContent( css );
};
nbsBlockBase.prototype.getIter = function() {
	return this._iter;
};
nbsBlockBase.prototype.beforeSave = function() {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].beforeSave();
		}
	}
};
nbsBlockBase.prototype.afterSave = function() {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].afterSave();
		}
	}
};
nbsBlockBase.prototype.mapElementsFromHtml = function($html, clb) {
	var self = this
	,	mapCall = function($el) {

		var element = self.getElementByIterNum( jQuery($el).data('iter-num') );
		if(element && element[ clb ]) {
			element[ clb ]();
		}
	};
	$html.find('.nbsEl').each(function(){
		mapCall( this );
	});
	if($html.hasClass('nbsEl')) {
		mapCall( $html );
	}
};
nbsBlockBase.prototype.replaceElement = function(element, toParamCode, type) {
	// Save current element content - in new element internal data
	var oldElContent = element.$().get(0).outerHTML
	,	oldElType = element.get('type')
	,	savedContent = element.$().data('pre-el-content');
	if(!savedContent)
		savedContent = {};
	savedContent[ oldElType ] = oldElContent;
	// Check if there are already saved prev. data for this type of element
	var newHtmlContent = savedContent[ type ] ? savedContent[ type ] : this.getParam( toParamCode );
	// Create and append new element HTML after current element
	var $newHtml = jQuery( newHtmlContent );
	$newHtml.insertAfter( element.$() );
	// Destroy current element
	var self = this;
	this.destroyElementByIterNum(element.getIterNum(), function(){
		// Init new element after prev. one was removed
		var newElements = self._initElementsForArea( $newHtml );
		for(var i = 0; i < newElements.length; i++) {
			// Save prev. updated content info - in new elements $()
			newElements[ i ].$().data('pre-el-content', savedContent);
		}
		self.contentChanged();
	});
};
nbsBlockBase.prototype.contentChanged = function(origin) {
	this._$.trigger('nbsBlockContentChanged', [this, origin]);
};
/**
 * Price table block base class
 */
nbsBlock_price_table.prototype.addColumn = function() {
	var $colsWrap = this._getColsContainer()
	,	$cols = this._getCols()
	,	$col = null
	,	self = this;
	if($cols.length) {
		var $lastCol = $cols.last();
		this.mapElementsFromHtml($lastCol, 'beforeSave');
		$col = $cols.last().clone();
		this.mapElementsFromHtml($lastCol, 'afterSave');
	} else {
		$col = jQuery( this.getParam('new_column_html') );
	}
	$colsWrap.append( $col );
	this._initElementsForArea( $col );
	this._initRemoveRowBtns( $col.find('.nbsCell') );
	this._refreshColNumbers();
	$cols = this._getCols();
	$cols.each(function(){
		var element = self.getElementByIterNum( jQuery(this).data('iter-num') );
		if(element) {
			// Update CSS style if required for updated classes
			element._setColor();
		}
	});
	this.checkColWidthPerc();
};
nbsBlock_price_table.prototype.getColsNum = function() {
	return this._getCols().length;
};
nbsBlock_price_table.prototype.addRow = function() {
	var $cols = this._getCols( true )
	,	self = this;
	$cols.each(function(){
		var $rowsWrap = jQuery(this).find('.nbsRows')
		,	$cell = jQuery( self.getParam('new_cell_html') );
		$rowsWrap.append( $cell );
		self._initElementsForArea( $cell );
		self._initRemoveRowBtns( $cell );
	});
	this.contentChanged();
};
nbsBlock_price_table.prototype.beforeSave = function() {
	nbsBlock_price_table.superclass.beforeSave.apply(this, arguments);
	this._destroyRemoveRowBtns();
};
nbsBlock_price_table.prototype.afterSave = function() {
	nbsBlock_price_table.superclass.afterSave.apply(this, arguments);
	this._initRemoveRowBtns();
};
nbsBlock_price_table.prototype._initRemoveRowBtns = function( $cell ) {
	var block = this;
	$cell = $cell ? $cell : this._$.find('.nbsCell');
	$cell.each(function(){
		if(jQuery(this).find('.nbsRemoveRowBtn').length) {
			jQuery(this).find('.nbsRemoveRowBtn').remove();
		}
		jQuery(this).append( jQuery('#nbsRemoveRowBtnExl').clone().removeAttr('id') );
		jQuery(this).hover(function(){
			jQuery(this).find('.nbsRemoveRowBtn').addClass('active');
		}, function(){
			jQuery(this).find('.nbsRemoveRowBtn').removeClass('active');
		});
		jQuery(this).find('.nbsRemoveRowBtn').click(function(){
			block._removeRow( jQuery(this).parents('.nbsCell:first'));
			return false;
		});
	});
};
nbsBlock_price_table.prototype._destroyRemoveRowBtns = function( $cell ) {
	this._$.find('.nbsRemoveRowBtn').remove();
};
nbsBlock_price_table.prototype._removeRow = function( $cell ) {
	var block = this
	,	cellIndex = $cell.index()
	,	$cols = this._getCols( true );
	$cols.each(function(){
		var $rowsWrap = jQuery(this).find('.nbsRows')
		,	$removeCell = $rowsWrap.find('.nbsCell:eq('+ cellIndex+ ')');
		if($removeCell && $removeCell.length) {
			var $elements = $removeCell.find('.nbsEl');
			$elements.each(function(){
				block.removeElementByIterNum( jQuery(this).data('iter-num') );
			});
			setTimeout(function(){
				$removeCell.animateRemoveNbs( g_nbsAnimationSpeed );
			}, g_nbsAnimationSpeed);	// Wait animation speed time to finally remove cell html element
		}
	});
	setTimeout(function(){
		block.contentChanged();
	}, g_nbsAnimationSpeed);
};
nbsBlock_price_table.prototype.getRowsNum = function() {
	return this._getCols().first().find('.nbsRows').find('.nbsCell').length;
};
nbsBlock_price_table.prototype._initHtml = function() {
	nbsBlock_price_table.superclass._initHtml.apply(this, arguments);
	var $colsWrap = this._getColsContainer()
	,	self = this;
	$colsWrap.sortable({
		items: '.nbsCol:not(.nbsTableDescCol)'
	,	axis: 'x'
	,	handle: '.nbsMoveHandler'
	,	start: function(e, ui) {
			_nbsSetSortInProgress( true );
			var dragElement = self.getElementByIterNum( ui.item.data('iter-num') );
			if(dragElement) {
				dragElement.onSortStart();
			}
		}
	,	stop: function(e, ui) {
			_nbsSetSortInProgress( false );
			var dragElement = self.getElementByIterNum( ui.item.data('iter-num') );
			if(dragElement) {
				dragElement.onSortStop();
			}
		}
	});
	// Set cols numbers for all columns
	this._refreshColNumbers();
	this._initRemoveRowBtns();
};
nbsBlock_price_table.prototype._refreshColNumbers = function() {
	var	self = this
	,	$cols = this._getCols()
	,	num = 1;
	$cols.each(function(){
		var element = self.getElementByIterNum( jQuery(this).data('iter-num') );
		if(element) {
			element._setColNum( num );
			var classes = jQuery(this).attr('class')
			,	newClasses = '';
			newClasses = (classes.replace(/nbsCol\-\d+/g, '')+ ' nbsCol-'+ num).replace(/\s+/g, ' ');
			jQuery(this).attr('class', newClasses);
		}
		num++;
	});
};
nbsBlock_price_table.prototype.getMaxColsSizes = function() {
	var $cols = this._getCols()
	,	sizes = {
			header: {sel: '.nbsColHeader'}
		,	desc: {sel: '.nbsColDesc'}
		,	rows: {sel: '.nbsRows'}
		,	cells: {sel: '.nbsCell'}
		,	footer: {sel: '.nbsColFooter'}
	};
	$cols.each(function(){
		for(var key in sizes) {
			var $entity = jQuery(this).find(sizes[ key ].sel);
			if($entity && $entity.length) {
				if(key == 'cells') {
					if(!sizes[ key ].height)
						sizes[ key ].height = [];
					var cellNum = 0;
					$entity.each(function(){
						var height = jQuery(this).height();
						if(!sizes[ key ].height[ cellNum ] || sizes[ key ].height[ cellNum ] < height) {
							sizes[ key ].height[ cellNum ] = height;
						}
						cellNum++;
					});
				} else {
					var height = $entity.height();
					if(!sizes[ key ].height || sizes[ key ].height < height) {
						sizes[ key ].height = $entity.height();
					}
				}
			}
		}
	});
	return sizes;
};
nbsBlock_price_table.prototype._updateFillColorFromColorpicker = function( color, ignoreAutoSave ) {
	this.setParam('bg_color', color.toHexString());
	this._updateFillColor( ignoreAutoSave );
};
nbsBlock_price_table.prototype._updateFillColor = function( ignoreAutoSave ) {
	this._rebuildCss();
	if(!ignoreAutoSave) {
		_nbsSaveCanvas();
	}
};
nbsBlock_price_table.prototype._updateTextColorFromColorpicker = function( color, ignoreAutoSave ) {
	this.setParam('text_color', color.toHexString());
	this._updateTextColor( ignoreAutoSave );
};
nbsBlock_price_table.prototype._updateTextColor = function( ignoreAutoSave ) {
	this._rebuildCss();
	/*this._$.css({
		'color': this.getParam('text_color')
	});*/
};
nbsBlock_price_table.prototype._getDescCol = function() {
	return this._$.find('.nbsTableDescCol');
};
nbsBlock_price_table.prototype.switchDescCol = function(state) {
	var $descCol = this._getDescCol();
	this.setParam('enb_desc_col', state ? 1 : 0);
	state 
		? $descCol.show()
		: $descCol.hide();
	this.checkColWidthPerc();
};
nbsBlock_price_table.prototype.setColsWidth = function(width, perc) {
	width = parseInt(width);
	if(width) {
		if(!perc) {
			this.setParam('col_width', width);
		}
		var $cols = this._getCols( true );
		if(perc) {
			width += '%';
		} else {
			width += 'px';
		}
		$cols.css({
			'width': width 
		});
	}
};
nbsBlock_price_table.prototype.checkColWidthPerc = function() {
	if(this.getParam('calc_width') === 'table') {
		this.setColWidthPerc();
	}
};
nbsBlock_price_table.prototype.setColWidthPerc = function() {
	var $cols = this._getCols( parseInt(this.getParam('enb_desc_col')) );
	this.setColsWidth( 100 / $cols.length, true );
};
nbsBlock_price_table.prototype.setTableWidth = function(width, measure) {
	if(width && parseInt(width)) {
		width = parseInt(width);
		this.setParam('table_width', width);
	} else {
		width = this.getParam('table_width');
	}
	if(measure) {
		this.setParam('table_width_measure', measure);
	} else {
		measure = this.getParam('table_width_measure');
	}
	this._$.width( width+ measure );
};
nbsBlock_price_table.prototype.setCalcWidth = function(type) {
	if(type) {
		this.setParam('calc_width', type);
	} else {
		type = this.getParam('calc_width');
	}
	switch(type) {
		case 'table':
			this.setTableWidth();
			this.setColWidthPerc();
			break;
		case 'col':
			this._$.width('auto');
			this.setColsWidth( this.getParam('col_width') );
			break;
	}
};
/**
 * Sliders block base class
 */
nbsBlock_sliders.prototype.beforeSave = function() {
	nbsBlock_sliders.superclass.beforeSave.apply(this, arguments);
	if(this._slider && this._slider.getCurrentSlide) {
		this._currentSlide = this._slider.getCurrentSlide();
	}
	this._destroySlider();
};
nbsBlock_sliders.prototype.afterSave = function() {
	nbsBlock_sliders.superclass.afterSave.apply(this, arguments);
	this._refreshSlides();
	this._initSlider();
};
nbsBlock_sliders.prototype._destroySlider = function() {
	if(this._slider) {
		this._slider.destroySlider();
	}
};
nbsBlock_sliders.prototype._clickMenuItem_add_slide = function(options, params) {
	params = params || {};
	var self = this;
	nbsCallWpMedia({
		id: this._$.attr('id')
	,	clb: function(opts, attach, imgUrl) {
			self.beforeSave();
			var value = self._data.params.new_slide_html.val;
			var newSlideHtml = jQuery( value );
			newSlideHtml.find('.nbsSlideImg').attr('src', imgUrl);
			self._$.find('.bxslider').append( newSlideHtml );
			var addedElements = self._initElementsForArea( newSlideHtml );
			// We added some elemtns, they were created and initialized - but all elements should be nulled, 
			// it was done in self.beforeSave(); for alll elements except those list. So, lets null them too, they will be re-initialized in 
			// code bellow - self.afterSave();
			if(addedElements && addedElements.length) {
				for(var i = 0; i < addedElements.length; i++) {
					addedElements[ i ].beforeSave();
				}
			}
			self.afterSave();
			_nbsSaveCanvas();
			// We add slide to the end of slider - so let's go to new slide right now
			self._slider.goToSlide( self._slider.getSlideCount() - 1 );
			if(params.clb && typeof(params.clb) == 'function') {
				params.clb();
			}
		}
	});
};
nbsBlock_sliders.prototype._clickMenuItem_edit_slides = function(options) {
	nbsUtils.showSlidesEditWnd( this );
};
nbsBlock_sliders.prototype._beforeInitHtml = function() {
	nbsBlock_sliders.superclass._beforeInitHtml.apply(this, arguments);
	this._refreshSlides();
};
nbsBlock_sliders.prototype._refreshSlides = function() {
	var iter = 1;
	this._$.find('.nbsSlide').each(function(){
		jQuery(this).data('slide-id', iter);
		iter++;
	});
	this._slides = this._$.find('.nbsSlide');
};
nbsBlock_sliders.prototype.getSlides = function() {
	return this._slides;
};
nbsBlock_sliders.prototype.getSliderShell = function() {
	return this._$.find('.nbsSliderShell');
};
/**
 * Galleries block
 */
nbsBlock_galleries.prototype.recalcRows = function() {
	var imgPerRow = parseInt(this.getParam('img_per_row'))
	,	rows = this._$.find('.row');
	
	for(var i = 0; i < rows.length; i++) {
		var rowImgsCount = jQuery(rows[ i ]).find('.nbsGalItem').length;
		if(rowImgsCount < imgPerRow && rows[ i + 1 ]) {
			// TODO: Make it to append not only first one, but all first elements count (imgPerRow - rowImgsCount)
			jQuery(rows[ i ]).append( jQuery(rows[ i + 1 ]).find('.nbsGalItem:first') );
		}
		if(rowImgsCount > imgPerRow) {
			if(rows[ i + 1 ]) {
				jQuery(rows[ i + 1 ]).prepend( jQuery(rows[ i ]).find('.nbsGalItem:last') );
			} else {
				jQuery('<div class="row" />').insertAfter( rows[ i ] ).prepend( jQuery(rows[ i ]).find('.nbsGalItem:last') );
			}
		}
	}
};
nbsBlock_galleries.prototype._initHtml = function() {
	nbsBlock_galleries.superclass._initHtml.apply(this, arguments);
	var self = this
	,	placeholderClasses = this._$.find('.nbsGalItem').attr('class');
	placeholderClasses += ' ui-state-highlight-gal-item';
	this._$.sortable({
		revert: true
	,	placeholder: placeholderClasses
	,	handle: '.nbsImgMoveBtn'
	,	items: '.nbsGalItem'
	,	start: function(event, ui) {
			var galleryItem = self._$.find('.nbsGalItem:first');
			ui.placeholder.css({
				'height': galleryItem.height()+ 'px'
			});
		}
	,	stop: function(event, ui) {
			self.recalcRows();
			setTimeout(function(){
				_nbsSaveCanvas();
			}, 400);
		}
	});
	this._initLightbox();
};
nbsBlock_galleries.prototype._clickMenuItem_add_gal_item = function(options, params) {
	params = params || {};
	var self = this;
	nbsCallWpMedia({
		id: this._$.attr('id')
	,	clb: function(opts, attach, imgUrl) {
			self.beforeSave();
			var value = self.getParam('new_item_html');
			value = twig({
				data: value
			}).render({
				block: self._data
			});
			var	newItemHtml = jQuery( value );
			newItemHtml.find('.nbsGalImg').attr('src', imgUrl).attr('data-full-img', attach.url);
			newItemHtml.find('.nbsGalLink').attr('href', attach.url);

			var appendToRow = self._$.find('.row:last')
			,	imgPerRow = parseInt(self.getParam('img_per_row'));
			if(appendToRow.find('.nbsGalItem').length >= imgPerRow) {
				jQuery('<div class="row" />').insertAfter( appendToRow );
				appendToRow = self._$.find('.row:last');
			}
			appendToRow.append( newItemHtml );
			self._initLightbox();
			var addedElements = self._initElementsForArea( newItemHtml );
			// We added some elemtns, they were created and initialized - but all elements should be nulled, 
			// it was done in self.beforeSave(); for alll elements except those list. So, lets null them too, they will be re-initialized in 
			// code bellow - self.afterSave();
			if(addedElements && addedElements.length) {
				for(var i = 0; i < addedElements.length; i++) {
					addedElements[ i ].beforeSave();
				}
			}
			self.afterSave();
			_nbsSaveCanvas();
			if(params.clb && typeof(params.clb) == 'function') {
				params.clb();
			}
		}
	});
};
nbsBlock_galleries.prototype._updateFillColorFromColorpicker = function( color, ignoreAutoSave ) {
	this.setParam('fill_color', color.toHexString());
	this._updateFillColor( ignoreAutoSave );
};
nbsBlock_galleries.prototype._updateFillColor = function( ignoreAutoSave ) {
	var fillColorEnb = this.getParam('fill_color_enb')
	,	captions = this._$.find('.nbsGalItemCaption');
	if(fillColorEnb) {
		var fillColor = this.getParam('fill_color');
		captions.css({
			'background-color': fillColor
		}).show();
	} else {
		captions.hide();
	}
};
nbsBlock_galleries.prototype._onShowFillColorPicker = function() {
	this._$.find('.nbsGalItemCaption').addClass('mce-edit-focus');
};
nbsBlock_galleries.prototype._onHideFillColorPicker = function() {
	this._$.find('.nbsGalItemCaption').removeClass('mce-edit-focus');
	_nbsSaveCanvas();
};
/**
 * Menu block base class
 */
nbsBlock_menus.prototype._clickMenuItem_add_menu_item = function(options, params) {
	this._showAddMenuItemWnd();
};
nbsBlock_menus.prototype._showAddMenuItemWnd = function() {
	nbsUtils.addMenuItemWndBlock = this;
	if(!nbsUtils.addMenuItemWnd) {
		nbsUtils.addMenuItemWnd = jQuery('#nbsAddMenuItemWnd').modal({
			show: false
		});
		nbsUtils.addMenuItemWnd.find('.nbsAddMenuItemSaveBtn').click(function(){
			var text = jQuery.trim( nbsUtils.addMenuItemWnd.find('[name="menu_item_text"]').val() )
			,	link = jQuery.trim( nbsUtils.addMenuItemWnd.find('[name="menu_item_link"]').val() )
			,	newWnd = nbsUtils.addMenuItemWnd.find('[name="menu_item_new_window"]').attr('checked') ? 1 : 0;
			if(text && text != '') {
				if(link && link != '') {
					var newItemHtml = jQuery( nbsUtils.addMenuItemWndBlock.getParam('new_item_html') )
					,	linkHtml = newItemHtml.find('a')
					,	menuMainRow = nbsUtils.addMenuItemWndBlock._$.find('.nbsMenuMain');
					link = nbsUtils.converUrl( link );
					linkHtml.attr('data-mce-href', link).attr('href', link).html( text );
					if(newWnd) {
						linkHtml.attr('target', '_blank');
					}
					menuMainRow.append( newItemHtml );
					var addedElements = nbsUtils.addMenuItemWndBlock._initElementsForArea( newItemHtml );
					_nbsSaveCanvas();
					nbsUtils.addMenuItemWnd.modal('hide');
				} else {
					nbsUtils.addMenuItemWnd.find('[name="menu_item_link"]').addClass('nbsInputError');
				}
			} else {
				nbsUtils.addMenuItemWnd.find('[name="menu_item_text"]').addClass('nbsInputError');
			}
			return false;
		});
		nbsInitCustomCheckRadio( nbsUtils.addMenuItemWnd );
	}
	nbsUtils.addMenuItemWnd.find('[name="menu_item_text"]').removeClass('nbsInputError').val(''),
	nbsUtils.addMenuItemWnd.find('[name="menu_item_link"]').removeClass('nbsInputError').val('');
	nbsCheckUpdate( nbsUtils.addMenuItemWnd.find('[name="menu_item_new_window"]').removeAttr('checked') );
	nbsUtils.addMenuItemWnd.modal('show');
};
/**
 * Subscribe block base class
 */
nbsBlock_subscribes.prototype.getFields = function() {
	if(!this._fields) {
		/*var fieldsStr = this.getParam('fields');
		this._fields = unserialize(fieldsStr);*/
		this._fields = this.getParam('fields');
	}
	return this._fields;
};
nbsBlock_subscribes.prototype.updateFields = function() {
	this.setParam('fields', this._fields);
};
nbsBlock_subscribes.prototype.setFieldParam = function(name, paramKey, paramVal) {
	this.getFields();
	if(this._fields.length) {
		for(var i = 0; i < this._fields.length; i++) {
			if(this._fields[i].name == name) {
				this._fields[i][ paramKey ] = paramVal;
				this.updateFields();
				break;
			}
		}
	}
};
nbsBlock_subscribes.prototype.setFieldLabel = function(name, label) {
	this.setFieldParam(name, 'label', label);
};
nbsBlock_subscribes.prototype.setFieldRequired = function(name, required) {
	this.setFieldParam(name, 'required', required);
};
nbsBlock_subscribes.prototype.addField = function(data) {
	this.getFields();
	this._fields.push( data );
};
nbsBlock_subscribes.prototype.removeField = function(name) {
	this.getFields();
	if(this._fields.length) {
		for(var i = 0; i < this._fields.length; i++) {
			if(this._fields[i].name == name) {
				this._fields.splice(i, 1);
				this.updateFields();
				break;
			}
		}
	}
};
nbsBlock_subscribes.prototype._afterInitElements = function() {
	nbsBlock_subscribes.superclass._afterInitElements.apply(this, arguments);
	var placeholderClasses = this._$.find('.nbsElInput:first').attr('class');
	placeholderClasses += ' ui-state-highlight-gal-item';
	this._$.find('.nbsFormShell').sortable({
		items: '.nbsElInput'
	,	handle: '.nbsMoveHandler'
	,	placeholder: placeholderClasses
	//,	containment: 'parent'
	//,	forceHelperSize: true
	//,	forcePlaceholderSize : true
	//,	cursorAt : {top: -20}
	,	start: function(event, ui) {
			var placeholderSub = ui.item.clone();
			placeholderSub.find('.nbsElMenu').remove();
			ui.placeholder.html( placeholderSub.html() );
		}
	,	stop: function(event, ui) {
			_nbsSaveCanvasDelay();
		}
	});
};
nbsBlock_subscribes.prototype._clickMenuItem_sub_settings = function(options, params) {
	this._showSubSettingsWnd();
};
nbsBlock_subscribes.prototype._showSubSettingsWnd = function() {
	nbsUtils.subSettingsWndBlock = this;
	var self = this;
	if(!nbsUtils.subSettingsWnd) {
		nbsUtils.subSettingsWnd = jQuery('#nbsSubSettingsWnd').modal({
			show: false
		});
		nbsUtils.subSettingsWnd.find('.nbsSubSettingsSaveBtn').click(function(){
			// TODO: Move such functionality (values to parameters) to separate class, or at least - to nbsUtils
			nbsUtils.subSettingsWnd.find('.nbsSettingFieldsShell').find('input, textarea, select').each(function(){
				var paramName = jQuery(this).attr('name')
				,	paramCheckbox = jQuery(this).attr('type') == 'checkbox'
				,	paramValue = '';
				if(paramCheckbox) {
					paramValue = jQuery(this).prop('checked') ? 1 : 0;
				} else {
					paramValue = jQuery(this).val();
				}
				if(paramName.indexOf('[]')) {
					paramName = str_replace(paramName, '[]', '');
				}
				nbsUtils.subSettingsWndBlock.setParam(paramName, paramValue);
			});
			_nbsSaveCanvas();
			nbsUtils.subSettingsWnd.modal('hide');
			return false;
		});
		nbsInitCustomCheckRadio( nbsUtils.subSettingsWnd );
		nbsUtils.subSettingsWnd.find('#nbsSubSettingsWndTabs').wpTabs();
		nbsUtils.subSettingsWnd.find('.nbsSettingFieldsShell [name=sub_dest]').change(function(){
			nbsUtils.subSettingsWnd.find('.nbsSettingFieldsShell .nbsSubDestRow').slideUp( self._animationSpeed );
			nbsUtils.subSettingsWnd.find('.nbsSettingFieldsShell .nbsSubDestRow.nbsSubDestRow_'+ jQuery(this).val()).slideDown( self._animationSpeed );
		});
		nbsUtils.subSettingsWnd.find('[name=sub_mailchimp_api_key]').change(function(){
			nbsUtils.subUpdateMailchimpLists();
		});
	}
	// TODO: Move such functionality (parameters to values) to separate class, or at least - to nbsUtils
	nbsUtils.subSettingsWnd.find('.nbsSettingFieldsShell').find('input, textarea, select').each(function(){
		var paramName = jQuery(this).attr('name')
		,	paramCheckbox = jQuery(this).attr('type') == 'checkbox'
		,	paramValue = nbsUtils.subSettingsWndBlock.getParam( paramName );
		if(paramCheckbox) {
			parseInt(paramValue) 
				? jQuery(this).attr('checked', 'checked')
				: jQuery(this).removeAttr('checked');
			nbsCheckUpdate( this );
		} else {
			jQuery(this).val( paramValue ? paramValue : jQuery(this).data('default') );
		}
	});
	nbsUtils.subSettingsWnd.find('.nbsSettingFieldsShell [name=sub_dest]').trigger('change');
	nbsUtils.subUpdateMailchimpLists();
	nbsUtils.subSettingsWnd.modal('show');
};
nbsBlock_subscribes.prototype._clickMenuItem_add_field = function(options, params) {
	this._showAddFieldWnd();
};
nbsBlock_subscribes.prototype._showAddFieldWnd = function() {
	nbsUtils.subAddFieldWndBlock = this;
	if(!nbsUtils.subAddFieldWnd) {
		nbsUtils.subAddFieldWnd = jQuery('#nbsAddFieldWnd').modal({
			show: false
		});
		nbsUtils.subAddFieldWnd.find('.nbsAddFieldSaveBtn').click(function(){
			var label = jQuery.trim( nbsUtils.subAddFieldWnd.find('[name="new_field_label"]').val() )
			,	name = jQuery.trim( nbsUtils.subAddFieldWnd.find('[name="new_field_name"]').val() )
			,	htmlType = nbsUtils.subAddFieldWnd.find('[name="new_field_html"]').val()
			,	required = nbsUtils.subAddFieldWnd.find('[name="new_field_reuired"]').prop('checked') ? 1 : 0;
			if(label && label != '') {
				if(name && name != '')  {
					nbsUtils.subAddFieldWndBlock.getFields();
					var newItemHtml = jQuery( nbsUtils.subAddFieldWndBlock.getParam('new_item_html') )
					,	inputHtml = newItemHtml.find('input')	// TODO: Make this work with all types of input (textarea, select, ...)
					,	formInputsShell = nbsUtils.subAddFieldWndBlock._$.find('.nbsFormFieldsShell');
					inputHtml.attr('placeholder', label).attr('name', name).attr('type', htmlType);
					if(required) {
						inputHtml.attr('required', '1');
					}
					formInputsShell.append( newItemHtml );
					var addedElements = nbsUtils.subAddFieldWndBlock._initElementsForArea( newItemHtml );
					nbsUtils.subAddFieldWndBlock.addField({
						name: name
					,	label: label
					,	html: htmlType
					,	required: required
					});
					_nbsSaveCanvas();
					nbsUtils.subAddFieldWnd.modal('hide');
				} else {
					nbsUtils.subAddFieldWnd.find('[name="new_field_name"]').addClass('nbsInputError');
				}
			} else {
				nbsUtils.subAddFieldWnd.find('[name="new_field_label"]').addClass('nbsInputError');
			}
			return false;
		});
		nbsInitCustomCheckRadio( nbsUtils.subAddFieldWnd );
	}
	nbsUtils.subAddFieldWnd.find('[name="new_field_label"]').removeClass('nbsInputError').val('');
	nbsUtils.subAddFieldWnd.find('[name="new_field_name"]').removeClass('nbsInputError').val('');
	nbsUtils.subAddFieldWnd.find('[name="new_field_html"]').removeClass('nbsInputError').val('text');
	nbsCheckUpdate( nbsUtils.subAddFieldWnd.find('[name="new_field_reuired"]').removeAttr('checked') );
	nbsUtils.subAddFieldWnd.modal('show');
};
/*nbsBlock_subscribes.prototype.beforeSave = function() {
	nbsBlock_subscribes.superclass.beforeSave.apply(this, arguments);
};
nbsBlock_subscribes.prototype.afterSave = function() {
	nbsBlock_subscribes.superclass.afterSave.apply(this, arguments);
};*/
nbsBlock_subscribes.prototype.getHtml = function() {
	var html = nbsBlock_subscribes.superclass.getHtml.apply(this, arguments);
	// We should replace start and end of our form each time we are doing save - as we need this content to be dynamicaly generated
	html = html.replace(/<\!--sub_form_start_open-->.+<\!--sub_form_start_close-->/g, '{{block.sub_form_start|raw}}');
	html = html.replace(/<\!--sub_form_end_open-->.+<\!--sub_form_end_close-->/g, '{{block.sub_form_end|raw}}');
	return html;
};
/**
 * Grid block base class
 */
nbsBlock_grids.prototype._getGridWrapper = function() {
	return this._$.find('.nbsGridWrapper');
};
nbsBlock_grids.prototype._getAllCols = function( $row ) {
	return $row.find('td.nbsCol');
};
nbsBlock_grids.prototype._getAllRows = function() {
	return this._getGridWrapper().find('tr.nbsRow');
};
nbsBlock_grids.prototype.addCol = function( params ) {
	params = params || {};
	var $newItem = jQuery( this._getDefaultColHtml() )
	,	$rows = null
	,	self = this;

	var element = params.element ? params.element : false;
	if(element) {
		var $element = element.$()
		,	addTo = params.addTo ? params.addTo : 'right'
		,	$row = $element.parents('.nbsRow:first')

		,	connectIndex = $element.index();
		$rows = $element.parents('.nbsContTbl:first').find('.nbsRow')
		$rows.each(function(){
			self._addColToRow( jQuery(this), $newItem, addTo, connectIndex);
		});
	}
	if($rows) {
		$rows.each(function(){
			self._recalcColsWidth({
				$row: jQuery(this)
			});
		});
	}
};
nbsBlock_grids.prototype._addColToRow = function( $row, $newItem, addTo, connectIndex ) {
	var $newItemClone = $newItem.clone();
	switch(addTo) {
		case 'left':
			$newItemClone.insertBefore( $row.find('.nbsCol').eq( connectIndex ) );
			break;
		case 'right': default:
			$newItemClone.insertAfter( $row.find('.nbsCol').eq( connectIndex ) );
			break;
	}
	this._initElementsForArea( $newItemClone );
};
nbsBlock_grids.prototype.mergeCols = function( params ) {
	params = params || {};
	var mergeTo = params.mergeTo ? params.mergeTo : 'right'
	,	element = params.element ? params.element : false
	,	$mergeTo = null
	,	$cell = element.$()
	,	mergeToTop = mergeTo == 'top';
	if(element) {
		switch( mergeTo ) {
			case 'left':
				$mergeTo = $cell.prev('.nbsCol');
				break;
			case 'right':
				$mergeTo = $cell.next('.nbsCol');
				break;
			case 'top':
				$mergeTo = $cell.parents('.nbsRow:first').prev('.nbsRow').find('.nbsCol').eq( $cell.index() );
				break;
			case 'bottom':
				$mergeTo = $cell.parents('.nbsRow:first').next('.nbsRow').find('.nbsCol').eq( $cell.index() );
				break;
		}
		if($mergeTo && $mergeTo.length) {
			var elementMergeTo = mergeToTop ? element : this.getElementByHtml( $mergeTo )
			,	childMoveElements = elementMergeTo._getChildElements();
			// Move all children to new parent cell
			if(childMoveElements && childMoveElements.length) {
				for(var i = 0; i < childMoveElements.length; i++) {
					childMoveElements[ i ].beforeSave();
					switch( mergeTo ) {
						case 'top':
							$mergeTo.append( childMoveElements[ i ].$() );
							break;
						case 'left':
							$cell.prepend( childMoveElements[ i ].$() );
							break;
						case 'right': case 'bottom':
							$cell.append( childMoveElements[ i ].$() );
							break;
					}
				}
				this._initElementsForArea( mergeToTop ? $mergeTo : $cell );
			}
			elementMergeTo.destroy();
			if( mergeToTop ) {
				element = this.getElementByHtml( $mergeTo );
			}
			var spanTo = toeInArrayNbs(mergeTo, ['left', 'right']) ? 'Col' : 'Row'
			,	span = parseInt( element['get'+ spanTo+ 'Span']() );
			if( !span ) {
				span = 2;
			} else {
				span++;
			}
			element['set'+ spanTo+ 'Span']( span );
		}
	}
};
nbsBlock_grids.prototype.alignCol = function( params ) {
	params = params || {};
	var alignTo = params.alignTo ? params.alignTo : 'center'
	,	element = params.element ? params.element : false;
	if(element) {
		switch( alignTo ) {
			case 'center': case 'left': case 'right':
				element.$().attr('align', alignTo);
				break;
			case 'top': case 'bottom': case 'middle':
				element.$().attr('valign', alignTo);
				break;
		}
	}
};
nbsBlock_grids.prototype.addRow = function( params ) {
	params = params || {};
	var $wrapper = this._getGridWrapper()
	,	addTo = params.addTo ? params.addTo : 'bottom'
	,	totalColsNum = 0
	,	$totalRows = this._getAllRows()
	,	$totalCols = this._getAllRows().last().find('.nbsCol');
	
	// Consider with merged columns
	if($totalRows && $totalRows.length) {
		$totalRows.each(function(){
			var $cols = jQuery(this).find('.nbsCol')
			,	currTotalColsNum = 0;
			if($cols && $cols.length) {
				$cols.each(function(){
					var colSpan = parseInt( jQuery(this).attr('colspan') );
					if( colSpan ) {
						currTotalColsNum += colSpan;
					} else {
						currTotalColsNum++;
					}
				});
			}
			if( currTotalColsNum > totalColsNum	)	// Select cols num from biggest row - to include all rowspans
				totalColsNum = currTotalColsNum;
		});
	}
	
	var colsHtml = []
	,	colHtml = this._getDefaultColHtml();
	for(var i = 0; i < totalColsNum; i++) {
		colsHtml.push( colHtml );
	}
	var $newItem = jQuery( '<tr class="nbsRow">'+ colsHtml.join('')+ '</tr>' );
	switch(addTo) {
		case 'top':
			$wrapper.prepend( $newItem );
			break;
		case 'bottom': default:
			$wrapper.append( $newItem );
			break;
	}
	this._recalcColsWidth({
		$row: $newItem
	});
	this._initElementsForArea( $newItem );
};
nbsBlock_grids.prototype._clickMenuItem_add_grid_col = function(options, params) {
	// TODO: Modify this to add columns not just to last row, but to all rows from block
	/*this.addCol({
		$row: this._getAllRows().last()	// Just add to to the last row
	});*/
};
nbsBlock_grids.prototype._clickMenuItem_add_grid_row = function(options, params) {
	this.addRow();
};
nbsBlock_grids.prototype._getDefaultColHtml = function() {
	return '<td class="nbsCol nbsEl" data-el="grid_col" height="60"></td>';
};
nbsBlock_grids.prototype._recalcColsWidth = function( params ) {
	params = params || {};
	var $allCols = this._getAllCols( params.$row )
	,	width = Math.floor( 100 / $allCols.length );
	$allCols.attr('width', width+ '%');
};
nbsBlock_grids.prototype._clickMenuItem_paddings = function(options, params) {
	nbsUtils.showPaddingsWnd( this );
};
nbsBlock_grids.prototype._getPadding = function( padding ) {
	if(typeof(padding) === 'string') {
		return this._$.find('.nbsContPadd[data-padd="'+ padding+ '"]');
	} else {
		var findDataSelector = [];
		for(var i = 0; i < padding.length; i++) {
			findDataSelector.push('[data-padd="'+ padding[ i ]+ '"]');
		}

		return this._$.find('.nbsContPadd').filter( findDataSelector.join(',') );
	}
};
nbsBlock_grids.prototype.switchPadding = function( padding, enb ) {
	var $padding = this._getPadding( padding );
	if(enb) {
		if(!$padding.length) {
			var paddingAttrs = {
					'class': 'nbsContPadd nbsEl'
				,	'data-el': 'col_padd'
				,	'data-padd': padding
			}
			,	paddingAttrsArr = [];
			switch( padding ) {
				case 'left': case 'right':
					var $topBottomPaddings = this._getPadding(['top', 'bottom']);
					if($topBottomPaddings.length) {
						var prevColspan = parseInt($topBottomPaddings.attr('colspan'));
						$topBottomPaddings.attr('colspan', (prevColspan ? ++prevColspan : 2));
					}
					paddingAttrs['width'] = this.getParam('padding_'+ padding)+ '%';
					paddingAttrs['height'] = '100%';
					break;
				case 'top': case 'bottom':
					paddingAttrs['height'] = this.getParam('padding_'+ padding);
					paddingAttrs['width'] = '100%';
					break;
			}
			for(var key in paddingAttrs) {
				if(typeof(paddingAttrs[ key ]) === 'string') {
					paddingAttrsArr.push(key+ '="'+ paddingAttrs[ key ]+ '"');
				}
			}
			$padding = jQuery( '<td '+ paddingAttrsArr.join(' ')+ ' />' );
			switch( padding ) {
				case 'left':
					this._$.find('.nbsMainRow').prepend( $padding );
					break;
				case 'right':
					this._$.find('.nbsMainRow').append( $padding );
					break;
				case 'top':
					this._$.find('.nbsTopRow').append( $padding );
					break;
				case 'bottom':
					this._$.find('.nbsBottomRow').append( $padding );
					break;
			}
			this._initElementsForArea( $padding );
		}
		this.setParam( 'enb_padding_'+ padding, 1 );
	} else {
		switch( padding ) {
			case 'left': case 'right':
				var $topBottomPaddings = this._getPadding(['top', 'bottom']);
				if($topBottomPaddings.length) {
					var colspan = parseInt($topBottomPaddings.attr('colspan'));
					if(colspan) {
						colspan--;
						if(colspan < 2) {
							$topBottomPaddings.removeAttr('colspan');
						} else {
							$topBottomPaddings.attr('colspan', colspan);
						}
					}
				}
				break;
		}
		$padding.remove();
		this.setParam( 'enb_padding_'+ padding, 0 );
	}
};
nbsBlock_grids.prototype.setPaddingSize = function( padding, size ) {
	var $padding = this._getPadding( padding );
	if($padding.length) {
		switch( padding ) {
			case 'left': case 'right':
				$padding.attr('width', size+ '%');
				break;
			case 'top': case 'bottom':
				$padding.attr('height', size);
				break;
		}
	}
	this.setParam( 'padding_'+ padding, size );
};
/**
 * Dynamic content block base class
 */
nbsBlock_content.prototype._clickMenuItem_dyn_content_sets = function(options) {
	nbsUtils.showDynContentSetsWnd( this );
};
nbsBlock_content.prototype._getDynArea = function() {
	return this._$.find('.nbsDynArea');
};
nbsBlock_content.prototype.updateDynContent = function() {
	var self = this;
	jQuery.sendFormNbs({
		data: {mod: 'octo', action: 'getBlockDynContent', block: this.getData(), canvasParams: g_nbsCanvas.get('params')}
	,	onSuccess: function(res) {
			if(!res.error) {
				var $dynArea = self._getDynArea();
				self._clearElementsForArea( $dynArea );
				$dynArea.html( res.html );
				self._initElementsForArea( $dynArea );
			}
		}
	});
};
nbsBlock_content.prototype._setAlign = function( align, ignoreAutoSave, menuItemHtml ) {
	nbsBlock_content.superclass._setAlign.apply(this, arguments);
	this._updateDynContAlign( align );
};
nbsBlock_content.prototype._updateDynContAlign = function( align ) {
	this._getDynArea().find('.nbsDynContCell').attr('align', align).css({
		'text-align': align
	});
};
nbsBlock_content.prototype._afterInitElements = function() {
	var self = this;
	this.$().bind('nbsBlockContentChanged', function(e, block, origin){
		var jqOrigin = origin.$ ? origin.$() : jQuery(origin);
		self._equalizeStaticText(block, jqOrigin);
	});
};
nbsBlock_content.prototype._equalizeStaticText = function(block, jqOrigin) {
	var originHtml = jqOrigin.html();
	var originId = jqOrigin.attr('id');
	var dataElId = jqOrigin.attr('data-el-id');
	if (originId) {
		block.$().find('[data-el-id="' + dataElId + '"]')
			.each(function(index, el){
				if (el.id == originId) return;
				el.innerHTML = originHtml;
			});
		this._setDynTemplateStaticText(dataElId, originHtml);
	}
};

nbsBlock_content.prototype._setDynTemplateStaticText = function(textDataElId, html) {
	var dynTpl = this.getParam('posts_tpl');
	var shellText = 'stat_txt_' + textDataElId + '_shell';
	var shellTextBegin = '<\!--' +shellText + '-->';
	var shellTextEnd = '<\!--\/' +shellText + '-->';
	var regExp = new RegExp(shellTextBegin + '([^]*)' + shellTextEnd, 'mi');
	var elHtml = regExp.exec(dynTpl);
	if (!elHtml) {
		console.warn("A shell <!--" + shellText + "--><!--/"  + shellText + "--> missing in dyn part of block ");
		return;
	}
	var openTag = /<(\w+)[^\>]*>/mi.exec(elHtml[1]);
	var closeTag = '</' + openTag[1] + '>';

	var newDynTpl = nbsHtmlEncode(dynTpl.replace(regExp, shellTextBegin + openTag[0] + html + closeTag + shellTextEnd));
	this.setParam('posts_tpl', newDynTpl);
};

