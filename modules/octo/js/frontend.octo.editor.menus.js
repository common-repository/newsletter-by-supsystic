function nbsMainMenu(element) {
	this._visible = false;
	this._$ = jQuery(element);
	this._animationSpeed = 300;
	this._mouseOver = false;
	this._catWidth = 120;
	this._blockWidth = 340;
	this._openBtnLeft = 30;
	this._init();
}
nbsMainMenu.prototype._init = function() {
	this._$.mouseover(jQuery.proxy(function(){
		this._mouseOver = true;
	}, this)).mouseleave(jQuery.proxy(function(){
		this._mouseOver = false;
	}, this));
};
nbsMainMenu.prototype.checkShow = function() {
	if(!this._visible) {
		this.show();
		return true;
	}
	return false;
};
nbsMainMenu.prototype.show = function() {
	this._visible = true;
	this._$.stop();
};
nbsMainMenu.prototype.checkHide = function() {
	if(this._visible) {
		this.hide();
	}
};
nbsMainMenu.prototype.hide = function() {
	this._visible = false;
	this._$.stop();
};
nbsMainMenu.prototype.isVisible = function() {
	return this._visible;
};
nbsMainMenu.prototype.getRaw = function() {
	return this._$;
};
nbsMainMenu.prototype.isMouseOver = function() {
	return this._mouseOver;
};
nbsMainMenu.prototype._setOpenBtnPos = function(pos) {
	jQuery('.nbsMainBarHandle').stop().animate({
		'left': pos+ 'px'
	}, this._animationSpeed);
	if(pos == this._openBtnLeft) {
		jQuery('.nbsMainBarHandle').removeClass('active').find('.octo-icon').addClass('icon-pluss-b').removeClass('icon-close-b');
	} else {
		jQuery('.nbsMainBarHandle').addClass('active').find('.octo-icon').addClass('icon-close-b').removeClass('icon-pluss-b');
	}
};
/**
 * Categories Menu Class (Main Menu)
 */
function nbsCategoriesMainMenu(element) {
	nbsCategoriesMainMenu.superclass.constructor.apply(this, arguments);
	this._subMenus = [];
	this._cidToSubId = {};
}
extendNbs(nbsCategoriesMainMenu, nbsMainMenu);
nbsCategoriesMainMenu.prototype.addSubMenu = function(subMenuObj) {
	var newSubObj = new nbsBlocksMainMenu(subMenuObj);
	var newIter = this._subMenus.push( newSubObj );
	this._cidToSubId[ newSubObj.getRaw().data('cid') ] = newIter - 1;
};
nbsCategoriesMainMenu.prototype.showSubByCid = function(cid) {
	if(this._subMenus[ this._cidToSubId[ cid ] ].checkShow()) {
		this._$.find('[data-id="'+ cid+ '"]').addClass('active');
		for(var i = 0; i < this._subMenus.length; i++) {
			if(this._subMenus[i].getCid() !== cid) {
				this.hideSubByCid( this._subMenus[i].getCid() );
			}
		}
	}
};
nbsCategoriesMainMenu.prototype.hideSubByCid = function(cid) {
	if(this._subMenus[ this._cidToSubId[ cid ] ].checkHide()) {
		this._$.find('[data-id="'+ cid+ '"]').removeClass('active');
	}
};
nbsCategoriesMainMenu.prototype.show = function() {
	nbsCategoriesMainMenu.superclass.show.apply(this, arguments);
	var self = this;
	this._$.animate({
		'left': '0px'
	}, this._animationSpeed, function(){
		self._$.find('.nbsMainBarInner').slimScroll({
			height: jQuery(window).height()
		});
	});
	this._setOpenBtnPos( this._catWidth + this._openBtnLeft );
};
nbsCategoriesMainMenu.prototype.checkHide = function() {
	if(this._visible && !this.isMouseOver()) {
		for(var i = 0; i < this._subMenus.length; i++) {
			if(this._subMenus[i].isMouseOver())
				return false;
		}
		this.hide();
		return true;
	}
	return false;
};
nbsCategoriesMainMenu.prototype.hide = function() {
	nbsCategoriesMainMenu.superclass.hide.apply(this, arguments);
	this._$.animate({
		'left': -this._catWidth+ 'px'
	}, this._animationSpeed);
	for(var i = 0; i < this._subMenus.length; i++) {
		this._subMenus[i].checkHide();
	}
	this._setOpenBtnPos( this._openBtnLeft );
};
nbsCategoriesMainMenu.prototype.isSubMenuVisible = function() {
	for(var i = 0; i < this._subMenus.length; i++) {
		if(this._subMenus[i].isVisible()) {
			return true;
		}
	}
	return false;
};
/**
 * Blocks Menu Class (Sub Menus)
 */
function nbsBlocksMainMenu(element) {
	nbsBlocksMainMenu.superclass.constructor.apply(this, arguments);
	this._cid = this._$.data('cid');
}
extendNbs(nbsBlocksMainMenu, nbsMainMenu);
nbsBlocksMainMenu.prototype.getCid = function() {
	return this._cid;
};
nbsBlocksMainMenu.prototype.show = function() {
	nbsBlocksMainMenu.superclass.show.apply(this, arguments);
	var self = this;
	this._$.animate({
		'left': this._catWidth+ 'px'
	}, this._animationSpeed, function(){
		self._$.find('.nbsBlockBarInner').slimScroll({
			height: jQuery(window).height()
		});
	});
	// Don't show this button when blocks menu is opened - for this plugin only
	//this._setOpenBtnPos( this._catWidth + this._openBtnLeft + this._blockWidth );
};
nbsBlocksMainMenu.prototype.hide = function() {
	nbsBlocksMainMenu.superclass.hide.apply(this, arguments);
	this._$.animate({
		'left': -this._blockWidth+ 'px'
	}, this._animationSpeed);
	// Don't show this button when blocks menu is opened - for this plugin only
	//this._setOpenBtnPos( this._catWidth + this._openBtnLeft );
};
nbsBlocksMainMenu.prototype.checkHide = function() {
	if(this._visible && !this.isMouseOver()) {
		this.hide();
		return true;
	}
	return false;
};