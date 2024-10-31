/**
 * Base block object - for extending
 * @param {object} blockData all block data from database (block database row)
 */
function nbsBlockBase(blockData) {
	this._data = blockData;
	this._$ = null;
	this._original$ = null;
	this._id = 0;
	this._iter = 0;
	this._elements = [];
	this._animationSpeed = 300;
	this._$menuItems = {};
	//this._oneTimeElementsInited = false;
}
nbsBlockBase.prototype.getData = function() {
	return this._data;
};
nbsBlockBase.prototype.get = function(key) {
	return this._data[ key ];
};
nbsBlockBase.prototype.getParams = function() {
	return this._data.params;
};
nbsBlockBase.prototype.getParam = function(key) {
	return this._data.params[ key ] ? this._data.params[ key ].val : this._getParamDefaultValue(key);
};
nbsBlockBase.prototype.setParam = function(key, value) {
	if(!this._data.params[ key ]) this._data.params[ key ] = {};
	this._data.params[ key ].val = value;
};
nbsBlockBase.prototype.isParamDefined = function(key) {
	return key in this._data.params;
};
nbsBlockBase.prototype.getRaw = function() {
	return this._$;
};
/**
 * Alias for getRaw method
 */
nbsBlockBase.prototype.$ = function() {
	return this.getRaw();
};
nbsBlockBase.prototype.setRaw = function(jqueryHtml) {
	this._$ = jqueryHtml;
	this._resetElements();
	this._initHtml();
	if(this.getParam('font_family')) {
		this._setFont( this.getParam('font_family') );
	}
};
nbsBlockBase.prototype._getParamDefaultValue = function(key) {
	switch (key) {
		case 'img_width_units':
			return 'px';
		default:
			return false;
	}
};

nbsBlockBase.prototype._initElements = function() {
	this._initElementsForArea( this._$ );
};
nbsBlockBase.prototype._initElementsForArea = function( $area ) {
	var block = this
	,	addedElements = [];
	$area = jQuery( $area );
	var initElement = function( $htmlEl ) {
		if($htmlEl.data('inited')) return;
		var elementCode = jQuery( $htmlEl ).data('el')
		,	elementClass = window[ 'nbsElement_'+ elementCode ];
		if(elementClass) {
			var newElement = new elementClass(jQuery( $htmlEl ), block);
			newElement._setCode(elementCode);
			var newIterNum = block._elements.push( newElement );
			addedElements.push( newElement );
			newElement.setIterNum( newIterNum - 1 );	// newIterNum == new length of _elements array, iterator number for element - is new length - 1
			$htmlEl.data('inited', 1);
		} else {
			if(g_nbsEdit)
				console.log('Undefined Element ['+ elementCode+ '] !!!');
		}
	};
	$area.find('.nbsEl').each(function(){
		initElement( jQuery(this) );
	});
	if($area.hasClass('nbsEl')) {
		initElement( $area );
	}
	this._afterInitElements();
	return addedElements;
};
nbsBlockBase.prototype._afterInitElements = function() {
	
};
nbsBlockBase.prototype._resetElements = function() {
	this._clearElements();
	this._initElements();
};
nbsBlockBase.prototype._clearElements = function() {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].destroy();
		}
		this._elements = [];
	}
};
nbsBlockBase.prototype._clearElementsForArea = function( $area ) {
	var block = this;
	var clearElement = function( $htmlEl ) {
		var element = block.getElementByHtml( jQuery($htmlEl) );
		if(element) {
			element.destroy();
		}
	};
	jQuery( $area ).find('.nbsEl').each(function(){
		clearElement(this);
	});
	if(jQuery( $area ).hasClass('nbsEl')) {
		clearElement( $area );
	}
};
nbsBlockBase.prototype.getElements = function() {
	return this._elements;
};
nbsBlockBase.prototype._initHtml = function() {

};
/**
 * ID number in list of canvas elements
 * @param {numeric} iter Iterator - number in all blocks array - for this element
 */
nbsBlockBase.prototype.setIter = function(iter) {
	this._iter = iter;
};
nbsBlockBase.prototype.showLoader = function(txt) {
	var loaderHtml = jQuery('#nbsBlockLoader');
	txt = txt ? txt : loaderHtml.data('base-txt');
	loaderHtml.find('.nbsBlockLoaderTxt').html( txt );
	loaderHtml.css({
		'height': this._$.height()
	,	'top': this._$.offset().top
	}).addClass('active');
};
nbsBlockBase.prototype.hideLoader = function() {
	var loaderHtml = jQuery('#nbsBlockLoader');
	loaderHtml.removeClass('active');
};
nbsBlockBase.prototype._setFont = function(fontFamily) {
	if(toeInArrayNbs(fontFamily, _nbsGetCanvas().getStandardFontsList()) === false) {
		var $fontLink = this._getFontLink();
		if($fontLink.data('font-family') === fontFamily) {	// It is already loaded
			return;
		}
		$fontLink.attr({
			'href': 'https://fonts.googleapis.com/css?family='+ encodeURIComponent(fontFamily)
		,	'data-font-family': fontFamily
		});
	}
	this._$.css({
		'font-family': fontFamily
	});
	this.setParam('font_family', fontFamily);
};
nbsBlockBase.prototype._getFontLink = function() {
	var $link = this._$.find('link.nbsFont');
	if(!$link.length) {
		$link = jQuery('<link class="nbsFont" rel="stylesheet" type="text/css" href="" />').appendTo( this._$ );
	}
	return $link;
};
/**
 * Finds nbsElement by dom element
 * @param domElement
 * @returns nbsElementBase|null
 */
nbsBlockBase.prototype.findElementByDom = function(domElement) {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			if (this._elements[ i ].$().get()[0] == domElement) {
				return this._elements[ i ];
			}
		}
	}
	return null;
};
/**
 * Price table block base class
 */
function nbsBlock_price_table(blockData) {
	this._increaseHoverFontPerc = 20;	// Increase font on hover effect, %
	nbsBlock_price_table.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_price_table, nbsBlockBase);
nbsBlock_price_table.prototype._getColsContainer = function() {
	return this._$.find('.nbsColsWrapper:first');
};
nbsBlock_price_table.prototype._getCols = function(includeDescCol) {
	return this._getColsContainer().find('.nbsCol'+ (includeDescCol ? '' : ':not(.nbsTableDescCol)'));
};
nbsBlock_price_table.prototype._afterInitElements = function() {
	nbsBlock_price_table.superclass._afterInitElements.apply(this, arguments);
	if(parseInt(this.getParam('enb_hover_animation'))) {
		this._initHoverEffect();
	}
};
nbsBlock_price_table.prototype._initHoverEffect = function() {
	/*if(_nbsIsEditMode()) {
		this.setParam('enb_hover_animation', 1);
		return;
	}*/
	var $cols = this._getCols()
	,	self = this;
	this._disableHoverEffect( $cols );
	$cols.bind('hover.animation', function(e){
		switch(e.type) {
			case 'mouseenter': case 'mousein':
				jQuery(this).addClass('hover');
				self._increaseHoverFont( jQuery(this) );
				break;
			case 'mouseleave': case 'mouseout':
				jQuery(this).removeClass('hover');
				self._backHoverFont( jQuery(this) );
				break;
		}
	});
	this.setParam('enb_hover_animation', 1);
};
nbsBlock_price_table.prototype._increaseHoverFont = function($col) {
	var self = this;
	$col.find('.nbsColDesc span').each(function(){
		var newFontSize = jQuery(this).data('new-font-size');
		if(!newFontSize) {
			var prevFontSize = jQuery(this).css('font-size')
			,	fontUnits = prevFontSize.replace(/\d+/, '')
			,	fontSize = parseInt(str_replace(prevFontSize, fontUnits, ''));
			if(fontSize && fontUnits) {
				newFontSize = Math.ceil(fontSize + (self._increaseHoverFontPerc * fontSize / 100));
				jQuery(this)
					.data('prev-font-size', prevFontSize)
					.data('font-units', fontUnits)
					.data('new-font-size', newFontSize);
			}
		}
		if(newFontSize) {
			jQuery(this).css('font-size', newFontSize+ jQuery(this).data('font-units'));
		}
	});
	if(_nbsIsEditMode()) {
		setTimeout(function(){
			var colElement = self.getElementByIterNum($col.data('iter-num'));
			if(colElement) {
				colElement.repositeMenu();
			}
		}, g_nbsHoverAnim);	// 300 - standard animation speed
	}
};
nbsBlock_price_table.prototype._backHoverFont = function($col) {
	$col.find('.nbsColDesc span').each(function(){
		var prevFontSize = jQuery(this).data('prev-font-size');
		if(prevFontSize) {
			jQuery(this).css('font-size', prevFontSize);
		}
	});
};
nbsBlock_price_table.prototype._disableHoverEffect = function($cols) {
	this.setParam('enb_hover_animation', 0);
	//if(_nbsIsEditMode()) return;
	$cols = $cols ? $cols : this._getCols();
	$cols.unbind('hover.animation');
};
/**
 * Covers block base class
 */
function nbsBlock_covers(blockData) {
	nbsBlock_covers.superclass.constructor.apply(this, arguments);

	//this._resizeBinded = false;
	this._bindResize();
}
extendNbs(nbsBlock_covers, nbsBlockBase);
nbsBlock_covers.prototype._initHtml = function() {
	nbsBlock_covers.superclass._initHtml.apply(this, arguments);
	this._onResize();
};
nbsBlock_covers.prototype._bindResize = function() {
	jQuery(window).resize(jQuery.proxy(function(){
		this._onResize();
	}, this));
};
nbsBlock_covers.prototype._onResize = function() {
	var wndHeight = jQuery(window).height();
	if (jQuery(window).width() < 745) 
		this._$.height( 'auto' );
	else
		this._$.height( wndHeight );
};
/**
 * Sliders block base class
 */
function nbsBlock_sliders(blockData) {
	nbsBlock_sliders.superclass.constructor.apply(this, arguments);
	this._slider = null;
	this._slides = null;
	this._currentSlide = 0;
}
extendNbs(nbsBlock_sliders, nbsBlockBase);
nbsBlock_sliders.prototype._initHtml = function() {
	nbsBlock_sliders.superclass._initHtml.apply(this, arguments);
	this._initSlider();
};
nbsBlock_sliders.prototype._initSlider = function() {
	var sliderElId = this._$.find('.bxslider').attr('id');
	this._slider = jQuery('#'+ sliderElId).bxSlider({
		/*infiniteLoop: false,
		hideControlOnEnd: false*/
		//adaptiveHeight: true
	});
	if(this._currentSlide) {
		this._slider.goToSlide( this._currentSlide );
	}
};
/**
 * Galleries block base class
 */
function nbsBlock_galleries(blockData) {
	nbsBlock_galleries.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_galleries, nbsBlockBase);
nbsBlock_galleries.prototype._initHtml = function() {
	nbsBlock_galleries.superclass._initHtml.apply(this, arguments);
	this._initLightbox();
};
nbsBlock_galleries.prototype._initLightbox = function() {
	this._$.find('.nbsGalLink:not(.nbsGalLinkOut)').prettyPhoto({
		slideshow: 5000
	,	social_tools: false
	,	deeplinking: false	// For now - let avoid placing hash in browser URL, maybe enable this latter
	});
};
/**
 * Banner block base class
 */
function nbsBlock_banners(blockData) {
	nbsBlock_banners.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_banners, nbsBlockBase);
/**
 * Banner block base class
 */
function nbsBlock_footers(blockData) {
	nbsBlock_footers.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_footers, nbsBlockBase);
/**
 * Menu block base class
 */
function nbsBlock_menus(blockData) {
	nbsBlock_menus.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_menus, nbsBlockBase);
/**
 * Subscribe block base class
 */
function nbsBlock_subscribes(blockData) {
	this._fields = null;
	nbsBlock_subscribes.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_subscribes, nbsBlockBase);
nbsBlock_subscribes.prototype._initHtml = function() {
	nbsBlock_subscribes.superclass._initHtml.apply(this, arguments);
	this._initForm();
};
nbsBlock_subscribes.prototype._getForm = function() {
	return this._$.find('.nbsSubscribeForm');
};
nbsBlock_subscribes.prototype._getFormShell = function() {
	return this._$.find('.nbsFormShell');
};
nbsBlock_subscribes.prototype._initForm = function() {
	// Some forms require usual submit
	if(toeInArrayNbs(this.getParam('sub_dest'), ['aweber'])) return;
	var form = this._getForm()
	,	self = this;
	form.submit(function(){
		var msgEl = jQuery(this).find('.nbsSubMsg')
		,	form = jQuery(this);
		jQuery(this).sendFormNbs({
			msgElID: msgEl
		,	msgCloseBtn: true
		,	hideLoader: true
		,	errorClass: 'alert alert-danger alert-dismissible'
		,	successClass: 'alert alert-success alert-dismissible'
		,	onBeforeSend: function() {
				self.showLoader();
			}
		,	onSuccess: function(res) {
				self.hideLoader();
				/*Add msg close btn*/
				
				//msgEl.append();
				if(!res.error) {
					msgEl.appendTo( self._getFormShell() );
					form.slideUp(self._animationSpeed, function(){
						form.remove();
					});
					/*setTimeout(function(){
						
					}, 2000);*/
				}
			}
		});
		return false;
	});
};
/**
 * Grid block base class
 */
function nbsBlock_grids(blockData) {
	nbsBlock_grids.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_grids, nbsBlockBase);
/**
 * Dynamic content block base class
 */
function nbsBlock_content(blockData) {
	nbsBlock_content.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_content, nbsBlockBase);
/**
 * Simple content block base class
 */
function nbsBlock_simple_content(blockData) {
	nbsBlock_simple_content.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsBlock_simple_content, nbsBlockBase);

