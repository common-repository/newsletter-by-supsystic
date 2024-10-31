function nbsElementBase(jqueryHtml, block) {
	this._iterNum = 0;
	this._id = 'el_'+ mtRand(1, 999999);
	this._animationSpeed = g_nbsAnimationSpeed;
	this._$ = jqueryHtml;
	this._block = block;
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = '';
	}
	this._innerImgsCount = 0;
	this._innerImgsLoaded = 0;
	//this._$menu = null;
	this._menu = null;
	this._menuClbs = {};
	if(typeof(this._menuClass) === 'undefined') {
		this._menuClass = 'nbsElementMenu';
	}
	this._menuOnBottom = false;
	this._code = 'base';

	this._initedComplete = false;
	this._editArea = null;
	if(typeof(this._isMovable) === 'undefined') {
		this._isMovable = false;
	}
	this._moveHandler = null;
	this._sortInProgress = false;
	if(typeof(this._showMenuEvent) === 'undefined') {
		this._showMenuEvent = 'click';
	}
	if(typeof(this._changeable) === 'undefined') {
		this._changeable = false;
	}
	if(g_nbsEdit) {
		this._init();
		this._initMenuClbs();
		this._initMenu();

		var images = this._$.find('img');
		if(images && (this._innerImgsCount = images.length)) {
			this._innerImgsLoaded = 0;
			var self = this;
			images.load(function(){
				self._innerImgsLoaded++;
				if(self._$.find('img').length == self._innerImgsLoaded) {
					self._afterFullContentLoad();
				}
			});
		}
	}
	this._onlyFirstHtmlInit();
	this._initedComplete = true;
}
nbsElementBase.prototype.getId = function() {
	return this._id;
};
nbsElementBase.prototype.getBlock = function() {
	return this._block;
};
nbsElementBase.prototype._onlyFirstHtmlInit = function() {
	if(this._$ && !this._$.data('first-inited')) {
		this._$.data('first-inited', 1);
		return true;
	}
	return false;
};
nbsElementBase.prototype.setIterNum = function(num) {
	this._iterNum = num;
	this._$.data('iter-num', num);
};
nbsElementBase.prototype.getIterNum = function() {
	return this._iterNum;
};
nbsElementBase.prototype.$ = function() {
	return this._$;
};
nbsElementBase.prototype.getCode = function() {
	return this._code;
};
nbsElementBase.prototype._setCode = function(code) {
	this._code = code;
};
nbsElementBase.prototype._init = function() {
	this._beforeInit();
};
nbsElementBase.prototype._beforeInit = function() {
	
};
nbsElementBase.prototype.destroy = function() {
	
};
nbsElementBase.prototype.get = function(opt) {
	return this._$.attr( 'data-'+ opt );	// not .data() - as it should be saved even after page reload, .data() will not create element attribute
};
nbsElementBase.prototype.set = function(opt, val) {
	this._$.attr( 'data-'+ opt, val );	// not .data() - as it should be saved even after page reload, .data() will not create element attribute
};
nbsElementBase.prototype._getEditArea = function() {
	if(!this._editArea) {
		this._editArea = this._$.children('.nbsElArea');
		if(!this._editArea.length) {
			this._editArea = this._$.find('.nbsInputShell');
		}
	}
	return this._editArea;
};
nbsElementBase.prototype._getOverlay = function() {
	return this._$.find('.nbsElOverlay');
};
nbsElementBase.prototype._isParent = function() {
	return parseInt(this.get('parent')) ? true : false;
};
/**
 * Standart button item
 */
function nbsElement_btn(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuBtnExl';
	}
	this._menuClass = 'nbsElementMenu_btn';
	this._haveAdditionBgEl = null;
	this.includePostLinks = true;
	nbsElement_btn.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_btn, nbsElementBase);
nbsElement_btn.prototype._onlyFirstHtmlInit = function() {
	if(nbsElement_btn.superclass._onlyFirstHtmlInit.apply(this, arguments)) {
		if(this.get('customhover-clb')) {
			var clbName = this.get('customhover-clb');
			if(typeof(this[clbName]) === 'function') {
				var self = this;
				this._getEditArea().hover(function(){
					self[clbName](true, this);
				}, function(){
					self[clbName](false, this);
				});
			}
		}
	}
};
nbsElement_btn.prototype._hoverChangeFontColor = function( hover, element ) {
	if(hover) {
		jQuery(element)
			.data('original-color', this._getEditArea().css('color'))
			.css('color', jQuery(element).parents('.nbsEl:first').attr('data-bgcolor'));	// Ugly, but only one way to get this value in dynamic way for now
	} else {
		jQuery(element)
			.css('color', jQuery(element).data('original-color'));
	}
};
nbsElement_btn.prototype._hoverChangeBgColor = function( hover, element ) {
	var parentElement = jQuery(element).parents('.nbsEl:first');	// Actual element html
	if(hover) {
		parentElement
			.data('original-color', parentElement.css('background-color'))
			.css('background-color', parentElement.attr('data-bgcolor'));	// Ugly, but only one way to get this value in dynamic way for now
	} else {
		parentElement
			.css('background-color', parentElement.data('original-color'));
	}
};
