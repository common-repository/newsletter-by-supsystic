function nbsElementMenu(menuOriginalId, element, btnsClb, params) {
	params = params || {};
	this._$ = null;;
	this._animationSpeed = g_nbsAnimationSpeed;
	this._menuOriginalId = menuOriginalId;
	this._element = element;
	this._btnsClb = btnsClb;
	this._visible = false;
	this._isMovable = false;
	this._changeable = params.changeable ? params.changeable : false;
	this._inAnimation = false;
	this._id = 'nbsElMenu_'+ mtRand(1, 99999);
	this._$colorPickerInput = this._$bgColorPickerInput = {};
	this._subMenuVisible = false;
	this.init();
}
nbsElementMenu.prototype.getId = function() {
	return this._id;
};
nbsElementMenu.prototype.setMovable = function(state) {
	this._isMovable = state;
};
nbsElementMenu.prototype.setChangeable = function(state) {
	this._changeable = state;
};
nbsElementMenu.prototype._afterAppendToElement = function() {
	if(this._changeable) {
		this._updateType();
	}
};
nbsElementMenu.prototype._updateType = function(refreshCheck) {
	if(this._changeable) {
		var type = this._element.get('type');
		this._$
			.find('[name=type]').removeAttr('checked')
			.filter('[value='+ type+ ']').attr('checked', 'checked');
	}
};
nbsElementMenu.prototype.$ = function() {
	return this._$;
};
nbsElementMenu.prototype.init = function() {
	var self = this
	,	$original = jQuery('#'+ this._menuOriginalId);
	if(!$original.data('icheck-cleared')) {
		$original.find('input').iCheck('destroy');
		$original.data('icheck-cleared', 1);
	}
	this._$ = $original
		.clone()
		.attr('id', this._id)
		.appendTo('body');
	this._afterAppendToElement();
	
	nbsInitCustomCheckRadio( this._$ );
	this._fixClickOnRadio();
	this.reposite();
	if(this._btnsClb) {
		for(var selector in this._btnsClb) {
			if(this._$.find( selector ).length) {
				this._$.find( selector ).click(function(){
					self._btnsClb[ jQuery(this).data('click-clb-selector') ]( self, this );
					return false;
				}).data('click-clb-selector', selector);
			}
		}
	}
	
	this._initSubMenus();
};
nbsElementMenu.prototype._fixClickOnRadio = function() {
	this._$.find('.nbsElMenuBtn').each(function(){
		if(jQuery(this).find('[type=radio]').length) {
			jQuery(this).find('[type=radio]').click(function(){
				jQuery(this).parents('.nbsElMenuBtn:first').click();
			});
		}
	});
};
nbsElementMenu.prototype._hideSubMenus = function() {
	if(!this._$) return;	// If menu was already destroyed, with destroy element for example
	if(!this._subMenuVisible) return;
	var menuAtBottom = this._$.hasClass('nbsElMenuBottom')
	,	menuOpenBottom = this._$.hasClass('nbsMenuOpenBottom')
	,	self = this;
	this._inAnimation = true;
	this._$.find('.nbsElMenuSubPanel[data-sub-panel]:visible').each(function(){
		jQuery(this).slideUp(self._animationSpeed);
	});
	this._$.removeClass('nbsMenuSubOpened');
	if(!menuAtBottom && !menuOpenBottom) {
		this._$.data('animation-in-process', 1).animate({
			'top': this._$.data('prev-top')
		}, this._animationSpeed, function(){
			self._$.data('animation-in-process', 0);
			self._inAnimation = false;
		});
	} else if(menuOpenBottom) {
		this._$.removeClass('nbsMenuOpenBottom');
		this._inAnimation = false;
	} else {
		this._inAnimation = false;
	}
	this._subMenuVisible = false;
};
nbsElementMenu.prototype._initSubMenus = function() {
	var self = this;
	if(this._$.find('.nbsElMenuBtn[data-sub-panel-show]').length) {
		this._$.find('.nbsElMenuBtn').click(function(){
			if(!jQuery(this).parent().hasClass('nbsElMenuSubPanel')) {
				self._hideSubMenus();
			}
		});
		this._$.find('.nbsElMenuBtn[data-sub-panel-show]').click(function(){
			var subPanelShow = jQuery(this).data('sub-panel-show')
			,	subPanel = self._$.find('.nbsElMenuSubPanel[data-sub-panel="'+ subPanelShow+ '"]')
			,	menuPos = self._$.position()
			,	menuAtBottom = self._$.hasClass('nbsElMenuBottom')
			,	menuTop = self._$.data('animation-in-process') ? self._$.data('prev-top') : menuPos.top;

			if(!subPanel.is(':visible')) {
				_nbsGetCanvas().setAlMenuAnim( true );
				self._inAnimation = true;
				subPanel.slideDown(self._animationSpeed, function(){
					if(!menuAtBottom) {
						var subPanelHeight = subPanel.outerHeight();
						// If menu is too hight to move top - don't do this
						if(menuTop - subPanelHeight < g_nbsTopBarH) {
							self._$.addClass('nbsMenuOpenBottom');
							_nbsGetCanvas().setAlMenuAnim( false );
							self._inAnimation = false;
						} else {
							self._$.data('prev-top', menuTop).animate({
								'top': menuTop - subPanelHeight
							}, self._animationSpeed, function(){
								_nbsGetCanvas().setAlMenuAnim( false );
								self._inAnimation = false;
							});
						}
					}
				});
				self._$.addClass('nbsMenuSubOpened');
				self._subMenuVisible = true;
			}
			return false;
		});
	}
};
nbsElementMenu.prototype.reposite = function() {
	var elOffset = this._element.$().offset()
	,	elWidth = this._element.$().width()
	//,	elHeight = this._element.$().height()
	,	width = this._$.width()
	,	height = this._$.height()
	,	left = elOffset.left - (width - elWidth) / 2
	,	top = elOffset.top - height;
	if(this._element.$().hasClass('hover')) {
		top -= g_nbsHoverMargin;
	}
	if(left < 0)
		left = 0;
	
	var elementOffset = this._element.$().offset();
	this._menuOnBottom = (elementOffset.top - height) <= g_nbsTopBarH || this._element.$().data('menu-to-bottom');
	if(this._menuOnBottom) {	// If menu is too hight - move it under it's element
		var elHeight = this._element.$().outerHeight();
		top += elHeight + height;
	}
	this._$.css({
		'left': (left)+ 'px'
	,	'top': (top)+ 'px'
	});
	if(this._menuOnBottom) {
		this._$.addClass('nbsElMenuBottom');
	}
	if(this._isMovable) {
		this._$.trigger('nbsElMenuReposite', [this, top, left]);
	}
};
nbsElementMenu.prototype.destroy = function() {
	if(this._$) {
		this._$.remove();
		this._$ = null;
	}
};
nbsElementMenu.prototype.show = function() {
	if(!this._$) return;	// If menu was already destroyed, with destroy element for example
	if(!this._visible && !_nbsSortInProgress() && !nbsUtils.isResizeInProgress()) {
		// Let's hide all other element menus in current block before show this one
		var blockElements = this.getElement().getBlock().getElements();
		for(var i = 0; i < blockElements.length; i++) {
			if(blockElements[ i ].menuInAnimation()) return;	// Menu is in animation - so we don't need to hide it
			blockElements[ i ].hideMenu();
		}
		this.reposite();
		// And now - show current menu
		this._$.addClass('active');
		this._visible = true;
	}
};
nbsElementMenu.prototype.inAnimation = function() {
	return this._inAnimation;
};
nbsElementMenu.prototype.hide = function() {
	if(!this._$) return;	// If menu was already destroyed, with destroy element for example
	if(this._visible) {
		this._hideSubMenus();
		this._$.removeClass('active');
		this._visible = false;
		if(this._isMovable) {
			this._$.trigger('nbsElMenuHide', this);
		}
	}
};
nbsElementMenu.prototype.getElement = function() {
	return this._element;
};
nbsElementMenu.prototype._initBgColorpicker = function(params) {
	params = params || {};
	params.picker = 'bg';
	this._initColorpicker( params );
};
nbsElementMenu.prototype._initColorpicker = function(params) {
	params = params || {};
	var self = this
	,	color = params.color ? params.color : this._element.get('color')
	,	colorPickerInputKey = params.picker == 'bg' ? '_$bgColorPickerInput' : '_$colorPickerInput'
	,	pickerSelector = params.picker == 'bg' ? '.nbsColorBtn .nbsColorpickerBgInput' : '.nbsColorBtn .nbsColorpickerInput:not(.nbsColorpickerBgInput)';

	this[ colorPickerInputKey ] = params.colorInp ? params.colorInp : this._$.find( pickerSelector );
	var options = jQuery.extend({
    		convertCallback: function (colors, a, b) {
	    		var rgbString = 'rgb('+ colors.webSmart.r+ ',' + colors.webSmart.g+ ',' + colors.webSmart.b+ ')';
	    		colors.tiny = new tinycolor( '#'+ colors.HEX );
	    		colors.tiny.toRgbString = function () {
	    			return rgbString;
	    		};
				var setColorMethod = this._nbsPicker == 'bg' ? '_setBgColor' : '_setColor';
	    		self._element[ setColorMethod ](rgbString);

	    		self[ colorPickerInputKey ].attr('value', rgbString);
	    	}
		,	_nbsPicker: params.picker
    	}
	,	g_nbsColorPickerOptions
    );
    
    this[ colorPickerInputKey ].css('background-color', color);
    this[ colorPickerInputKey ].attr('value', color);
    this[ colorPickerInputKey ].colorPicker(options);
};
nbsElementMenu.prototype._updateBgColorPicker = function(color, params) {
	params = params || {};
	params.picker = 'bg';
	this._updateColorPicker( color, params );
};
nbsElementMenu.prototype._updateColorPicker = function(color, params) {
	params = params || {};
	var colorPickerInputKey = params.picker == 'bg' ? '_$bgColorPickerInput' : '_$colorPickerInput';
	this[ colorPickerInputKey ].css('background-color', color);
    this[ colorPickerInputKey ].attr('value', color);
};
nbsElementMenu.prototype.isVisible = function() {
	return this._visible;
};
/**
 * Try to find color picker in menu, if find - init it
 * TODO: Make this work for all menus, that using colopickers
 */
/*nbsElementMenu.prototype._initColorPicker = function(){
	
};*/
function nbsElementMenu_btn(menuOriginalId, element, btnsClb) {
	nbsElementMenu_btn.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_btn, nbsElementMenu);
nbsElementMenu_btn.prototype._afterAppendToElement = function() {
	nbsElementMenu_btn.superclass._afterAppendToElement.apply(this, arguments);

	this.$().find('.nbsPostLinkDisabled')
		.removeClass('nbsPostLinkDisabled')
		.addClass('nbsPostLinkList');

	// Link settings
	var self = this
	,	btnLink = this._element._getEditArea()
	,	linkInp = this._$.find('[name=btn_item_link]')
	,	titleInp = this._$.find('[name=btn_item_title]')
	,	newWndInp = this._$.find('[name=btn_item_link_new_wnd]');

	linkInp.val( btnLink.attr('href') ).change(function(){
		btnLink.attr('href', jQuery(this).val());
	});
	titleInp.val( btnLink.attr('title') ).change(function(){
		btnLink.attr('title', jQuery(this).val());
	});
	btnLink.attr('target') == '_blank' ? newWndInp.attr('checked', 'checked') : newWndInp.removeAttr('checked');
	newWndInp.change(function(){
		jQuery(this).attr('checked') ? btnLink.attr('target', '_blank') : btnLink.removeAttr('target');
	});
	// Color settings
	this._initColorpicker({
		color: this._element.get('bgcolor')
	});
};
function nbsElementMenu_icon(menuOriginalId, element, btnsClb) {
	nbsElementMenu_icon.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_icon, nbsElementMenu);
nbsElementMenu_icon.prototype._afterAppendToElement = function() {
	nbsElementMenu_icon.superclass._afterAppendToElement.apply(this, arguments);

	this.$().find('.nbsPostLinkDisabled')
		.removeClass('nbsPostLinkDisabled')
		.addClass('nbsPostLinkList');

	var self = this
	,	iconSizeID = ['fa-lg', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x']
	,	iconSize = {
		'fa-lg': '1.33333333em'
	,	'fa-2x': '2em'
	,	'fa-3x': '3em'
	,	'fa-4x': '4em'
	,	'fa-5x': '5em'
	}
	,	$icon = this._element._$.find('.fa').first();

	if ($icon.length) {
		var	iconClasses = $icon.attr("class").split(' ').reverse()
		,	currentIconSize = undefined;
		
		for (var i in iconClasses) {
			if (iconSizeID.indexOf(iconClasses[i]) != -1) {
				currentIconSize = iconClasses[i];
				break;
			}
		}

		if (currentIconSize)
			this._$.find('[data-size="' + currentIconSize + '"]').addClass('active');
	}

	this._$.on('click', '[data-size]', function () {
		var classSize = jQuery(this).attr('data-size')
		,	$icon = self._element._$.find('.fa').first();

		if (! $icon.length || ! classSize) return;

		$icon.removeClass(iconSizeID.join(' '));
		$icon.addClass(classSize);
		$icon.css('font-size', iconSize[classSize]);
		self._$.find('[data-size].active').removeClass('active');
		self._$.find('[data-size="' + classSize + '"]').addClass('active');
	});
	
	// Open links library
	this._$.find('.nbsIconLibBtn').click(function(){
		nbsUtils.showIconsLibWnd( self._element );
		return false;
	});
	// Color settings
	this._initColorpicker();
	// Link settings
	var btnLink = this._element._getLink()
	,	linkInp = this._$.find('[name=icon_item_link]')
	,	titleInp = this._$.find('[name=icon_item_title]')
	,	newWndInp = this._$.find('[name=icon_item_link_new_wnd]');

	if(btnLink) {
		linkInp.val( btnLink.attr('href') );
		titleInp.val( btnLink.attr('title') );
		btnLink.attr('target') == '_blank' ? newWndInp.attr('checked', 'checked') : newWndInp.removeAttr('checked');
		btnLink.click(function(e){
			e.preventDefault();
		});
	}
	linkInp.change(function(){
		self._element._setLinkAttr('href', jQuery(this).val());
	});
	titleInp.change(function(){
		self._element._setLinkAttr('title', jQuery(this).val());
	});
	newWndInp.change(function(){
		self._element._setLinkAttr('target', jQuery(this).prop('checked') ? true : false);
	});
};
function nbsElementMenu_img(menuOriginalId, element, btnsClb) {
	nbsElementMenu_img.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_img, nbsElementMenu);
nbsElementMenu_img.prototype._afterAppendToElement = function() {
	nbsElementMenu_img.superclass._afterAppendToElement.apply(this, arguments);

	this.$().find('.nbsPostLinkDisabled')
		.removeClass('nbsPostLinkDisabled')
		.addClass('nbsPostLinkList');
	
	this.getElement().get('type') === 'video'
		? this.$().find('[name=type][value=video]').attr('checked', 'checked')
		: this.$().find('[name=type][value=img]').attr('checked', 'checked');

	var self = this;
	var btnLink = this._element._getLink()
		,	linkInp = this._$.find('[name=icon_item_link]')
		,	titleInp = this._$.find('[name=icon_item_title]')
		,	newWndInp = this._$.find('[name=icon_item_link_new_wnd]');

	if(btnLink) {
		linkInp.val( btnLink.attr('href') );
		titleInp.val( btnLink.attr('title') );
		btnLink.attr('target') == '_blank' ? newWndInp.attr('checked', 'checked') : newWndInp.removeAttr('checked');
		btnLink.click(function(e){
			e.preventDefault();
		});
	}

	linkInp.change(function(){
		self._element._setLinkAttr('href', jQuery(this).val());
	});

	titleInp.change(function(){
		self._element._setLinkAttr('title', jQuery(this).val());
	});

	newWndInp.change(function(){
		self._element._setLinkAttr('target', jQuery(this).prop('checked') ? true : false);
	});
};
function nbsElementMenu_table_cell(menuOriginalId, element, btnsClb) {
	nbsElementMenu_table_cell.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_table_cell, nbsElementMenu);
nbsElementMenu_table_cell.prototype._afterAppendToElement = function() {
	nbsElementMenu_table_cell.superclass._afterAppendToElement.apply(this, arguments);
	var type = this.getElement().get('type');
	if(!type)
		type = 'txt';
	this._$.find('[name=type][value='+ type+ ']').attr('checked', 'checked');
};
/**
 * Table col menu
 */
function nbsElementMenu_table_col(menuOriginalId, element, btnsClb) {
	nbsElementMenu_table_col.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_table_col, nbsElementMenu);
nbsElementMenu_table_col.prototype._afterAppendToElement = function() {
	nbsElementMenu_table_col.superclass._afterAppendToElement.apply(this, arguments);
	var self = this;
	// Enb/Dslb fill color
	var $enbFillColorCheck = this._$.find('[name=enb_fill_color]');
	$enbFillColorCheck.change(function(){
		self.getElement().set('enb-color', jQuery(this).attr('checked') ? 1 : 0);
		self.getElement()._setColor();	// Just update it from existing color
		return false;
	});
	parseInt(this.getElement().get('enb-color'))
		? $enbFillColorCheck.attr('checked', 'checked')
		: $enbFillColorCheck.removeAttr('checked');
	// Color settings
	this._initColorpicker();
	// Enb/Dslb badge
	var $enbBadgeCheck = this._$.find('[name=enb_badge_col]');
	$enbBadgeCheck.change(function(){
		//self.getElement().set('enb-badge', jQuery(this).attr('checked') ? 1 : 0);
		if(jQuery(this).attr('checked')) {
			self.getElement()._setBadge();	// Just update it from existing color
		} else {
			self.getElement()._disableBadge();
		}
		return false;
	});
	parseInt(this.getElement().get('enb-badge'))
		? $enbBadgeCheck.attr('checked', 'checked')
		: $enbBadgeCheck.removeAttr('checked');
	// Badge click
	this._btnsClb['.nbsColBadgeBtn'] = function() {
		
		self.getElement()._showSelectBadgeWnd();
	};
};
function nbsElementMenu_table_cell_icon(menuOriginalId, element, btnsClb) {
	nbsElementMenu_table_cell_icon.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_table_cell_icon, nbsElementMenu_icon);
/**
 * Grid column menu
 */
function nbsElementMenu_grid_col(menuOriginalId, element, btnsClb) {
	nbsElementMenu_grid_col.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_grid_col, nbsElementMenu);
nbsElementMenu_grid_col.prototype._afterAppendToElement = function() {
	nbsElementMenu_grid_col.superclass._afterAppendToElement.apply(this, arguments);
	var self = this;
	// Enb/Dslb fill color
	var enbFillColorCheck = this._$.find('[name=enb_fill_color]');
	enbFillColorCheck.change(function(){
		self.getElement().set('enb-color', jQuery(this).attr('checked') ? 1 : 0);
		self.getElement()._setColor();	// Just update it from existing color
		return false;
	});
	parseInt(this.getElement().get('enb-color'))
		? enbFillColorCheck.attr('checked', 'checked')
		: enbFillColorCheck.removeAttr('checked');
	// Color settings
	this._initColorpicker();
	// Enb/Dslb bg img
	var enbBgImgCheck = this._$.find('[name=enb_bg_img]');
	enbBgImgCheck.change(function(){
		self.getElement().set('enb-bg-img', jQuery(this).attr('checked') ? 1 : 0);
		self.getElement()._setImg();	// Just update it from existing image
		return false;
	});
	parseInt(this.getElement().get('enb-bg-img'))
		? enbBgImgCheck.attr('checked', 'checked')
		: enbBgImgCheck.removeAttr('checked');
};
/**
 * Delimiter menu
 */
function nbsElementMenu_delimiter(menuOriginalId, element, btnsClb) {
	nbsElementMenu_delimiter.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_delimiter, nbsElementMenu);
nbsElementMenu_delimiter.prototype._afterAppendToElement = function() {
	nbsElementMenu_delimiter.superclass._afterAppendToElement.apply(this, arguments);
	// Color settings
	this._initColorpicker();
};
/**
 * Dunamic Text element menu
 */
function nbsElementDynTxt_menu(menuOriginalId, element, btnsClb) {
	this._$fontSizeSliderInput = null;
	this._$fontSizeSlider = null;
	this._$fontItalicBtn = null;
	this._$fontBoldBtn = null;
	nbsElementDynTxt_menu.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementDynTxt_menu, nbsElementMenu);
nbsElementDynTxt_menu.prototype._afterAppendToElement = function() {
	nbsElementDynTxt_menu.superclass._afterAppendToElement.apply(this, arguments);
	// Color settings
	var dynProps = this._element.getDynProps()
	,	block = this._element.getBlock();
	if(dynProps) {
		for(var prop in dynProps) {
			switch(prop) {
				case 'color':
					this._initColorpicker({
						'color': block.getParam( dynProps[ prop ] )
					});
					break;
				case 'font-size':
					this._initFontSizeSelect({
						'fontSize': block.getParam( dynProps[ prop ] )
					});
					break;
				case 'font-italic':
					this._initFontItalic({
						'value': block.getParam( dynProps[ prop ] ) 
					});
					break;
				case 'font-bold':
					this._initFontBold({ 
						'value': block.getParam( dynProps[ prop ] ) 
					});
					break;
				case 'background-color':
					this._initBgColorpicker({
						'color': block.getParam( dynProps[ prop ] )
					});
					break;
			}
		}
	}
	this._checkUnusedElements();
};
nbsElementDynTxt_menu.prototype._checkUnusedElements = function() {
	var dynProps = this._element.getDynProps()
	,	possibleElements = ['color', 'font-size', 'font-italic', 'font-bold', 'background-color'];
	for(var i = 0; i < possibleElements.length; i++) {
		if(typeof(dynProps[ possibleElements[i] ]) === 'undefined') {
			this._$.find('.nbsElMenuBtn[data-for="'+ possibleElements[i]+ '"],.nbsElMenuSubPanel[data-sub-panel="'+ possibleElements[i]+ '"]').remove();
		}
	}
};
nbsElementDynTxt_menu.prototype._initFontSizeSelect = function( params ) {
	params = params || {};
	this._$fontSizeSliderInput = this._$.find('[name="font_size"]');
	this._$fontSizeSlider = jQuery('<div class="nbsSliderInpShell" />');
	var $parent = this._$fontSizeSliderInput.parent()
	,	fontSize = params.fontSize ? params.fontSize : this._element.get('font-size')
	,	self = this;
	fontSize = parseFloat( fontSize );
	$parent.append( this._$fontSizeSlider );
	this._$fontSizeSlider.slider({
		min: 1
	,	max: 99
	,	step: 1
	,	value: fontSize
	,	slide: function(event, ui) {
			self._$fontSizeSliderInput.val( ui.value ).change();
		}
	});
	this._$fontSizeSliderInput.val( fontSize );
	this._$fontSizeSliderInput.change(function(){
		var val = parseInt( jQuery(this).val() );
		if(val) {
			self._element._setFontSize( val );
		}
	});
};
nbsElementDynTxt_menu.prototype._initFontItalic = function( params ) {
	params = params || {};
	this._$fontItalicBtn = this._$.find('.nbsFontItalicBtn');
	var italic = parseInt( params.value ? params.value : this._element.get('font-italic') )
	,	self = this;
	if(italic)
		this._switchFontItalicBtn( italic );
	this._$fontItalicBtn.click(function(){
		var italic = parseInt(self._element.get('font-italic'));
		italic = italic ? 0 : 1;
		self._element.set('font-italic', italic);
		self._switchFontItalicBtn( italic );
		self._element._setFontItalic( italic );
	});
};
nbsElementDynTxt_menu.prototype._switchFontItalicBtn = function(italic) {
	italic ? this._$fontItalicBtn.addClass('active') : this._$fontItalicBtn.removeClass('active');
};
nbsElementDynTxt_menu.prototype._initFontBold = function( params ) {
	params = params || {};
	this._$fontBoldBtn = this._$.find('.nbsFontBoldBtn');
	var bold = parseInt( params.value ? params.value : this._element.get('font-bold') )
	,	self = this;
	if(bold)
		this._switchFontBoldBtn( bold );
	this._$fontBoldBtn.click(function(){
		var bold = parseInt(self._element.get('font-bold'));
		bold = bold ? 0 : 1;
		self._element.set('font-bold', bold);
		self._switchFontBoldBtn( bold );
		self._element._setFontBold( bold );
	});
};
nbsElementDynTxt_menu.prototype._switchFontBoldBtn = function(bold) {
	bold ? this._$fontBoldBtn.addClass('active') : this._$fontBoldBtn.removeClass('active');
};

/**
 * Socail Icons menu
 */
/*function nbsElementMenu_social_icons(menuOriginalId, element, btnsClb) {
	nbsElementMenu_social_icons.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_social_icons, nbsElementMenu);
nbsElementMenu_social_icons.prototype._afterAppendToElement = function() {
	nbsElementMenu_social_icons.superclass._afterAppendToElement.apply(this, arguments);
	// Color settings
	this._initColorpicker();
};*/
/**
 * Removable Table Cell (<td>) menu
 */
function nbsElementMenu_td(menuOriginalId, element, btnsClb) {
	nbsElementMenu_td.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElementMenu_td, nbsElementMenu);
nbsElementMenu_td.prototype._afterAppendToElement = function() {
	nbsElementMenu_td.superclass._afterAppendToElement.apply(this, arguments);
};
