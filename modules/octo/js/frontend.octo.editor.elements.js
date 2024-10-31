/**
 * Destroy current element
 */
nbsElementBase.prototype.destroy = function(clb) {
	if(this._$) {
		var childElements = this._getChildElements();
		if(childElements) {
			for(var i = 0; i < childElements.length; i++) {
				childElements[ i ]._remove();
			}
		}
		var self = this;
		this._$.slideUp(this._animationSpeed, function(){
			self._remove();
			if(clb && typeof(clb) === 'function') {
				clb();
			}
			// Don't do this here: removing one block will trigger destroy for each element - really bad idea
			//_nbsSaveCanvas();
		});
	}
};
nbsElementBase.prototype._remove = function() {
	if(this._showMenuEvent == 'click') {
		jQuery(document).unbind('click.menu_el_click_hide_'+ this.getId());
	}
	this._destroyMenu();
	this._$.remove();
	this._$ = null;
	this._afterDestroy();
	this._block.removeElementByIterNum( this.getIterNum() );
};
nbsElementBase.prototype._getChildElements = function() {
	var $allFoundHtml = this._$.find('.nbsEl');
	if($allFoundHtml && $allFoundHtml.length) {
		var foundElements = []
		,	selfBlock = this.getBlock();
		$allFoundHtml.each(function(){
			var element = selfBlock.getElementByIterNum( jQuery(this).data('iter-num') );
			if(element) {
				foundElements.push( element );
			}
		});
		return foundElements.length ? foundElements : false;
	}
	return false;
};
nbsElementBase.prototype._afterDestroy = function() {
	
};
nbsElementBase.prototype.beforeSave = function() {
	this._destroyMoveHandler();
};
nbsElementBase.prototype.afterSave = function() {
	this._initMoveHandler();
};
nbsElementBase.prototype._initMenu = function() {
	if(this._menuOriginalId && this._menuOriginalId != '') {
		this._initMenuClbs();
		var menuParams = {
			changeable: this._changeable
		};
		if(!window[ this._menuClass ]) {
			console.log('Can not find menu class for '+ this._menuClass+ '!');
			return;
		}
		this._menu = new window[ this._menuClass ]( this._menuOriginalId, this, this._menuClbs, menuParams );
		if(!this._initedComplete) {
			var self = this;
			switch(this._showMenuEvent) {
				case 'hover':
					this._$.hover(function(){
						if(_nbsGetCanvas().getElMenuAnim()) return false;
						clearTimeout(jQuery(this).data('hide-menu-timeout'));
						self.showMenu();
					}, function(){
						if(_nbsGetCanvas().getElMenuAnim()) return false;
						jQuery(this).data('hide-menu-timeout', setTimeout(function(){
							self.hideMenu();
						}, 1000));	// Let it be visible 1 second more
					});
					this._menu.$().hover(function(){
						if(_nbsGetCanvas().getElMenuAnim()) return false;
						clearTimeout(jQuery(self._$).data('hide-menu-timeout'));
					}, function(){
						if(_nbsGetCanvas().getElMenuAnim()) return false;
						jQuery(self._$).data('hide-menu-timeout', setTimeout(function(){
							self.hideMenu();
						}, 1000));	// Let it be visible 1 second more
					});
					break;
				case 'click': default:
					this._$.click(function(e){
						e.stopPropagation();
						e.preventDefault();
						self.showMenu();
					});
					jQuery(document).bind('click.menu_el_click_hide_'+ this.getId(), jQuery.proxy(this._closeMenuOnDocClick, this));
					break;
			}
		}
		if(this._isMovable) {
			this._initMoveHandler();
			this._initMovableMenu();
		}

		this.initPostLinks(this._menu._$);
	}
};
nbsElementBase.prototype.initPostLinks = function($menu) {
	if (! this.includePostLinks) return;

	var $linkTab = $menu.find('.nbsPostLinkList')
	,	$field = null
	,	fieldSelector = $linkTab.attr('data-postlink-to');

	if (! fieldSelector.length) return;

	if (fieldSelector.indexOf(':parent') == 0) {
		fieldSelector = fieldSelector.substring(7, fieldSelector.length).trim();

		$field = $linkTab.parent().find(fieldSelector);
	} else {
		$field = jQuery(fieldSelector);
	}

	if (! $field.length) return;

	this.showPostsLinks($linkTab);

	$linkTab.css({
		height: 120
	});

	$linkTab.on('click', 'li', function () {
		var $item = jQuery(this)
		,	url = $item.attr('data-value');

		if (! url) return;

		$field.val(url);

		$field.change();
	});

	$linkTab.slimScroll({
		height: 120
	,	railVisible: true
	,	alwaysVisible: true
	,	allowPageScroll: true
	,	color: '#f72497'
	,	opacity: 1
	,	distance: 0
	,	borderRadius: '3px'
	});

	$linkTab.parent('.slimScrollDiv')
		.addClass('nbsPostLinkRoot')
		.hide();

	var $rootTab = $linkTab.parent('.nbsPostLinkRoot');

	/** Hide and show handlers **/
	var ignoreHide = false
	,	isFocus = false;

	$field.on('postlink.hide', function () {
		$rootTab.hide();

		$linkTab.hide();

		$field.trigger('postlink.hide:after');
	});

	$field.focus(function () {
		$field.trigger('postlink.show');

		$rootTab.show();

		$linkTab.show();

		isFocus = true;

		$field.trigger('postlink.show:after');
	});

	$rootTab.hover(function () {
		ignoreHide = true;
	}, function () {
		ignoreHide = false;

		if (! isFocus) {
			$field.trigger('postlink.hide');
		}
	});

	$field.blur(function () {
		isFocus = false;

		if (!ignoreHide) {
			$field.trigger('postlink.hide');
		}
	});
};
nbsElementBase.prototype.escapeString = function  (str) {
	return jQuery('<div/>').text(str).html();
};
nbsElementBase.prototype.showPostsLinks = function($tab) {
	if (! $tab.find('ul').length) {
		$tab.html('<ul></ul>');
	}

	$tab.find('ul').html('');

	for (var i in nbsEditor.posts) {
		$tab.find('ul')
			.append(
				'<li data-value="' + this.escapeString(nbsEditor.posts[i].url) + '">' +
					'<span>' + this.escapeString(nbsEditor.posts[i].title) + '</span>' +
				'</li>'
			);
	}
};
nbsElementBase.prototype._closeMenuOnDocClick = function(e, element) {
	if(!this._menu.isVisible()) return;
	var $target = jQuery(e.target);
	if(!this.$().find( $target ).length && !this.getMenu().$().find($target).length) {
		this.hideMenu();
	}
};
nbsElementBase.prototype.getMenu = function() {
	return this._menu;
};
nbsElementBase.prototype._initMovableMenu = function() {
	this._menu.setMovable(true);
	this._menu.$().bind('nbsElMenuReposite', function(e, menu, top, left){
		var element = menu.getElement()
		,	$element = element.$()
		,	$menu = menu.$()
		,	elWidth = $element.width()
		,	menuWidth = $menu.width()
		,	menuHeight = $menu.height();
		var placePos = menu.$().find('.nbsElMenuMoveHandlerPlace').position()
		,	moveTop = -1 * menuHeight + placePos.top;
		if($element.hasClass('hover')) {
			moveTop -= g_nbsHoverMargin;
		}
		element._moveHandler.css({
			'top': moveTop
		,	'left': ((elWidth - menuWidth) / 2) + placePos.left - 10
		}).addClass('active');
	}).bind('nbsElMenuHide', function(e, menu){
		var element = menu.getElement();
		if(!element._sortInProgress) {
			element._moveHandler.removeClass('active');
		}
	});
};
nbsElementBase.prototype.onSortStart = function() {
	this._sortInProgress = true;
	this._moveHandler.addClass('sortInProgress');
	this._menu.hide();
};
nbsElementBase.prototype.onSortStop = function() {
	this._sortInProgress = false;
	this._moveHandler.removeClass('sortInProgress');
	this._menu.show();
};
nbsElementBase.prototype._initMenuClbs = function() {
	var self = this;
	this._menuClbs['.nbsRemoveElBtn'] = function() {
		self.destroy();
	};
	if(this._changeable) {
		this._menuClbs['.nbsTypeTxtBtn'] = function() {
			self.getBlock().replaceElement(self, 'txt_item_html', 'txt');
		};
		this._menuClbs['.nbsTypeImgBtn'] = function() {
			self.getBlock().replaceElement(self, 'img_item_html', 'img');
		};
		this._menuClbs['.nbsTypeIconBtn'] = function() {
			self.getBlock().replaceElement(self, 'icon_item_html', 'icon');
		};
	}
};
nbsElementBase.prototype._initMoveHandler = function() {
	if(this._isMovable && !this._moveHandler) {
		this._moveHandler = jQuery('#nbsMoveHandlerExl').clone().removeAttr('id').appendTo( this._$ );
	}
};
nbsElementBase.prototype._destroyMoveHandler = function() {
	if(this._isMovable) {
		this._moveHandler.remove();
		this._moveHandler = null;
	}
};
nbsElementBase.prototype._afterFullContentLoad = function() {
	//sthis.repositeMenu();
};
nbsElementBase.prototype._destroyMenu = function() {
	if(this._menu) {
		this._menu.destroy();
		this._menu = null;
	}
};
nbsElementBase.prototype.showMenu = function() {
	if(this._menu) {
		this._menu.show();
	}
};
nbsElementBase.prototype.hideMenu = function() {
	if(this._menu) {
		this._menu.hide();
	}
};
nbsElementBase.prototype.menuInAnimation = function() {
	if(this._menu) {
		return this._menu.inAnimation();
	}
	return false;
};
nbsElementBase.prototype.setMovable = function(state) {
	this._isMovable = state;
};
nbsElementBase.prototype.repositeMenu = function() {
	if(this._menu) {
		this._menu.reposite();
	}
};
/**
 * Text element
 */
function nbsElement_txt(jqueryHtml, block, params) {
	this._elId = null;
	this._editorElement = null;
	this._editor = null;
	this.includePostLinks = true;
	this._params = params || {};
	this._editorToolbarBtns = [
		['alignleft', 'aligncenter', 'alignright', 'alignjustify'], ['octo_fontselect'], ['octo_fontsizeselect'], ['bold', 'italic', 'underline', 'strikethrough'], ['superscript', 'subscript'], ['octo_image'], ['octo_link'], ['forecolor'], ['octo_elementremove']
	];
	this._editorPlugins = ['octo_textcolor', 'octo_link', 'octo_fontselect', 'octo_fontsizeselect', 'wpgallery', 'octo_image'];
	if(this._params.excludeBtns) {
		this._excludeBtns( this._params.excludeBtns );
	}
	nbsElement_txt.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_txt, nbsElementBase);
nbsElement_txt.prototype._excludeBtns = function( exclude ) {
	if(typeof(exclude) === 'string') {
		exclude = [ exclude ];
	}
	for(var i = 0; i < exclude.length; i++) {
		for(var j = 0; j < this._editorToolbarBtns.length; j++) {
			var foundBtn = toeInArray( exclude[ i ], this._editorToolbarBtns[ j ] );
			if(foundBtn !== -1) {
				this._editorToolbarBtns[ j ].splice( foundBtn, 1 );
				if(!this._editorToolbarBtns[ j ].length) {
					this._editorToolbarBtns.splice( j , 1 );
				}
			}
		}
	}
};
nbsElement_txt.prototype._afterEditorInit = function(editor) {
	var self = this;
	editor.addButton('octo_elementremove', {
		classes: 'glyphicon glyphicon-trash btn',
		icon: false,
		title: 'Remove'
	,	onclick: function(e) {
			self.destroy();
		}
	});
};
nbsElement_txt.prototype._init = function() {
	nbsElement_txt.superclass._init.apply(this, arguments);
	var id = this._$.attr('id')
	,	self = this;
	if(!id || id == '') {
		this._$.attr('id', 'nbsTxt_'+ mtRand(1, 99999));
	}
	var toolbarBtns = [];
	for(var i = 0; i < this._editorToolbarBtns.length; i++) {
		toolbarBtns.push( typeof(this._editorToolbarBtns[i]) === 'string' ? this._editorToolbarBtns[i] : this._editorToolbarBtns[i].join(' ') );
	}
	var contentChangeNotification = function(e){
		e.target._nbsChanged = true;
		if(e.target._nbsChangeTimeout) {
			clearTimeout( e.target._nbsChangeTimeout );
		}
		e.target._nbsChangeTimeout = setTimeout(function(){
			var block = self.getBlock();
			if(block) {
				block.contentChanged(e.target);
			}
			if(typeof(self._params.contentChangedClb) === 'function') {
				self._params.contentChangedClb( e.target );
			}
		}, 1000);
	};
	this._editorElement = this._$.tinymce({
		inline: true
	// ' |  | ' is panel buttons delimiter
	,	toolbar: toolbarBtns.join(' |  | ')
	,	menubar: false
	,	plugins: this._editorPlugins.join(' ')
	,	fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt 64pt 72pt'
	,	skin : 'octo'
	,	convert_urls: false
	,	setup: function(ed) {
			self._editor = ed;
			ed.on('blur', function(e) {
				if(e.target._nbsChanged) {
					e.target._nbsChanged = false;
					_nbsSaveCanvas();
				}
			});
			ed.on('change', function(e) {
				contentChangeNotification(e);
			});
			ed.on('keyup', function(e) {
				var selectionCoords = getSelectionCoords();
				nbsMceMoveToolbar( self._editorElement.tinymce(), selectionCoords.x );
				/* when typing text the _change_ notification do not happens so here we make it additionally */
				contentChangeNotification(e);
			});
			ed.on('click', function(e) {
				nbsMceMoveToolbar( self._editorElement.tinymce(), e.clientX );

				if (ed.theme.panel.hasOwnProperty('isInitPostlinkClick')) return;

				var handler = function () {
					ed.theme.panel.isInitPostlinkClick = true;

					var $fieldWp = jQuery('#' + self._$.attr('id') + 'nbsPostLinkList');

					if ($fieldWp.length) {
						ed.theme.panel.off('click', handler);	
						
						self.initPostLinks($fieldWp.parents('.mce-container'));					
					}
				};

				ed.theme.panel.on('click', handler);
			});
			/*ed.on('focus', function(e) {
				
			});*/
			if(self._afterEditorInit) {
				self._afterEditorInit( ed );
			}
		}
	});
	this._$.removeClass('mce-edit-focus');
	// Do not allow drop anything it text element outside content area
	this._$.bind('dragover drop', function(event){
		event.preventDefault();
	});
};
nbsElement_txt.prototype.getEditorElement = function() {
	return this._editorElement;
};
nbsElement_txt.prototype.getEditor = function() {
	return this._editor;
};
nbsElement_txt.prototype.beforeSave = function() {
	nbsElement_txt.superclass.beforeSave.apply(this, arguments);
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	this._elId = this._$.attr('id');
	this._$
		.removeAttr('id')
		.removeAttr('contenteditable')
		.removeAttr('spellcheck')
		.removeClass('mce-content-body mce-edit-focus');
};
nbsElement_txt.prototype.afterSave = function() {
	nbsElement_txt.superclass.afterSave.apply(this, arguments);
	if(this._elId) {
		this._$
			.attr('id', this._elId)
			.attr('contenteditable', 'true')
			.attr('spellcheck', 'false')
			.addClass('mce-content-body');;
	}
};
/**
 * Image element
 */
function nbsElement_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuImgExl';
	}
	this._menuClass = 'nbsElementMenu_img';
	this.includePostLinks = true;
	nbsElement_img.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_img, nbsElementBase);
nbsElement_img.prototype._init = function() {
	nbsElement_img.superclass._init.apply(this, arguments);
	var self = this
	,	$img = this._getImg();
	$img.load(function(){
		var $this = jQuery(this);
		nbsUtils.setResizable( $this );
		$this.data('resizable-set', 1);
	});
	setTimeout(function(){
		if(!$img.data('resizable-set')) {
			nbsUtils.setResizable( $img );
			$img.data('resizable-set', 1);
		}
	}, 2000);
};
nbsElement_img.prototype.beforeSave = function() {
	nbsElement_img.superclass.beforeSave.apply(this, arguments);
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	nbsUtils.destroyResizable( this._getImg() );
};
nbsElement_img.prototype.afterSave = function() {
	nbsElement_img.superclass.afterSave.apply(this, arguments);
	nbsUtils.setResizable( this._getImg() );
};
nbsElement_img.prototype._beforeImgChange = function(opts, attach, imgUrl, imgToChange) {
	
};
nbsElement_img.prototype._afterImgChange = function(opts, attach, imgUrl, imgToChange) {
	
};
nbsElement_img.prototype._initMenuClbs = function() {
	nbsElement_img.superclass._initMenuClbs.apply(this, arguments);
	var self = this;
	this._menuClbs['.nbsImgChangeBtn'] = function() {
		self.set('type', 'img');
		self._getImg().show();
		self._getVideoFrame().remove();
		nbsCallWpMedia({
			id: self._$.attr('id')
		,	clb: function(opts, attach, imgUrl) {
				var $imgToChange = self._getImg();
				self._innerImgsLoaded = 0;
				self._beforeImgChange( opts, attach, imgUrl, $imgToChange );
				$imgToChange.attr('src', imgUrl);
				nbsUtils.resetResizableData( $imgToChange );
				self._afterImgChange( opts, attach, imgUrl, $imgToChange );
				self._block.contentChanged();
			}
		});
	};
	this._menuClbs['.nbsImgVideoSetBtn'] = function() {
		self.set('type', 'video');
		self._buildVideo( self._menu.$().find('[name=video_link]').val() );
	};
};
nbsElement_img.prototype._buildVideo = function(url) {
	url = url ? jQuery.trim( url ) : false;
	if(url) {
		var $editArea = this._getEditArea()
		,	$videoFrame = this._getVideoFrame( $editArea )
		,	$img = this._getImg( $editArea )
		,	src = nbsUtils.urlToVideoSrc( url );
		$videoFrame.attr({
			'src': src
		,	'width': $img.width()
		,	'height': $img.height()
		}).show();
		$img.hide();
	}
};
nbsElement_img.prototype._getVideoFrame = function( editArea ) {
	editArea = editArea ? editArea : this._getEditArea();
	var videoFrame = editArea.find('iframe.nbsVideo');
	if(!videoFrame.length) {
		videoFrame = jQuery('<iframe class="nbsVideo" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen />').appendTo( editArea );
	}
	return videoFrame;
};
nbsElement_img.prototype._getImg = function($editArea) {
	$editArea = $editArea ? $editArea : this._getEditArea();
	return $editArea.find('img');
};
nbsElement_img.prototype._initMenu = function() {
	nbsElement_img.superclass._initMenu.apply(this, arguments);
	var self = this;
	this._menu.$().find('[name=video_link]').change(function(){
		self._buildVideo( jQuery(this).val() );
	}).keyup(function(e){
		if(e.keyCode == 13) {	// Enter
			self._buildVideo( jQuery(this).val() );
		}
	});
};
nbsElement_img.prototype._getLink = function() {
	var $link = this._$.find('a.nbsLink');
	return $link.length ? $link : false;
};
nbsElement_img.prototype._setLinkAttr = function(attr, val) {
	switch(attr) {
		case 'href':
			if(val) {
				var $link = this._createLink();
				$link.attr(attr, val);
			} else
				this._removeLink();
			break;
		case 'title':
			var $link = this._createLink();
			$link.attr(attr, val);
			break;
		case 'target':
			var $link = this._createLink();
			val ? $link.attr('target', '_blank') : $link.removeAttr('target');
			break;
	}
};
nbsElement_img.prototype._createLink = function() {
	var $link = this._getLink();
	if(!$link) {
		$link = jQuery('<a class="nbsLink" />').append( this._$.find('img') ).appendTo( this._$ );
		$link.click(function(e){
			e.preventDefault();
		});
	}
	return $link;
};
nbsElement_img.prototype._removeLink = function() {
	var $link = this._getLink();
	if($link) {
		this._$.append( $link.find('.nbsInputShell') );
		$link.remove();
	}
};
/**
 * Gallery image element
 */
function nbsElement_gal_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuGalItemExl';
	}
	nbsElement_gal_img.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_gal_img, nbsElement_img);
nbsElement_gal_img.prototype._afterDestroy = function() {
	nbsElement_gal_img.superclass._afterDestroy.apply(this, arguments);
	this._block.recalcRows();
};
nbsElement_gal_img.prototype._afterImgChange = function(opts, attach, imgUrl, imgToChange) {
	nbsElement_gal_img.superclass._afterImgChange.apply(this, arguments);
	imgToChange.attr('data-full-img', attach.url);
	imgToChange.parents('.nbsGalLink:first').attr('href', attach.url);
};
nbsElement_gal_img.prototype._updateLink = function() {
	var newLink = jQuery.trim( this._menu.$().find('[name=gal_item_link]').val() )
	,	linkHtml = this._$.find('.nbsGalLink');
	if(newLink && newLink != '') {
		newLink = nbsUtils.converUrl( newLink );
		linkHtml.attr('href', newLink);
		var newWnd = this._menu.$().find('[name=gal_item_link_new_wnd]').attr('checked');
		newWnd ? linkHtml.attr('target', '_blank') : linkHtml.removeAttr('target');
		linkHtml.addClass('nbsGalLinkOut').attr('data-link', newLink).attr('data-new-wnd', newWnd ? 1 : 0);
		this._block._initLightbox();
	} else {
		linkHtml
			.attr('href', this._$.find('img').data('full-img'))
			.removeAttr('target data-link data-new-wnd')
			.removeClass('nbsGalLinkOut');
	}
};
/**
 * Menu item element
 */
function nbsElement_menu_item(jqueryHtml, block) {
	/*if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuGalItemExl';
	}*/
	nbsElement_menu_item.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_menu_item, nbsElement_txt);
nbsElement_menu_item.prototype._afterEditorInit = function(editor) {
	var self = this;
	editor.addButton('tables_remove', {
		title: 'Remove'
	,	onclick: function(e) {
			self.destroy();
		}
	});
};
nbsElement_menu_item.prototype._beforeInit = function() {
	this._editorToolbarBtns.push('tables_remove');
};
/**
 * Menu item image
 */
function nbsElement_menu_item_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuMenuItemImgExl';
	}
	nbsElement_menu_item_img.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_menu_item_img, nbsElement_img);
/**
 * Input item
 */
function nbsElement_input(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuInputExl';
	}
	if(typeof(this._isMovable) === 'undefined') {
		this._isMovable = true;
	}
	nbsElement_input.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_input, nbsElementBase);
nbsElement_input.prototype._init = function() {
	nbsElement_input.superclass._init.apply(this, arguments);
	var saveClb = function(element) {
		jQuery(element).attr('placeholder', jQuery(element).val());
		jQuery(element).val('');
		_nbsSaveCanvasDelay();
	};
	this._getInput().focus(function(){
		jQuery(this).val(jQuery(this).attr('placeholder'));
	}).blur(function(){
		if(jQuery(this).data('saved')) {
			jQuery(this).data('saved', 0);
			return;
		}
		saveClb(this)
	}).keyup(function(e){
		if(e.keyCode == 13) {	// Enter
			saveClb(this);
			jQuery(this).data('saved', 1).trigger('blur');	// We must blur from element after each save in any case
		}
	});
};
nbsElement_input.prototype._getInput = function() {
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	// TODO: Modify this to return all fields types
	return this._$.find('input');
};
nbsElement_input.prototype._initMenu = function(){
	nbsElement_input.superclass._initMenu.apply(this, arguments);
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	var self = this
	,	menuReqCheck = this._menu.$().find('[name="input_required"]');
	menuReqCheck.change(function(){
		var required = jQuery(this).attr('checked');
		if(required) {
			self._getInput().attr('required', '1');
		} else {
			self._getInput().removeAttr('required');
		}
		self._block.setFieldRequired(self._getInput().attr('name'), (helperChecked ? 1 : 0));
		_nbsSaveCanvasDelay();
	});
	self._getInput().attr('required')
		? menuReqCheck.attr('checked', 'checked')
		: menuReqCheck.removeAttr('checked');
	nbsCheckUpdate( menuReqCheck );
};
nbsElement_input.prototype.destroy = function() {
	// Remove field from block fields list at first
	var name = this._getInput().attr('name');
	this._block.removeField( name );
	nbsElement_input.superclass.destroy.apply(this, arguments);
};
/**
 * Input button item
 */
function nbsElement_input_btn(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuInputBtnExl';
	}
	if(typeof(this._isMovable) === 'undefined') {
		this._isMovable = false;
	}
	nbsElement_input_btn.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_input_btn, nbsElementBase);
nbsElement_input_btn.prototype._getInput = function() {
	// TODO: Modify this to return all fields types
	var $btn = this._$.find('input');
	if(!$btn || !$btn.length) {
		$btn = this._$.find('button');
	}
	return $btn;
};
nbsElement_input_btn.prototype._init = function() {
	nbsElement_input_btn.superclass._init.apply(this, arguments);
	var isIconic = parseInt(this.get('iconic'));
	var saveClb = function(element) {
		jQuery(element).attr('type', 'submit');
		_nbsSaveCanvasDelay();
	};
	var self = this;
	this._getInput().click(function(){
		if(isIconic) {
			nbsUtils.showIconsLibWnd( self );
		}
		return false;
	}).focus(function(){
		if(isIconic) return;
		var value = jQuery(this).val();
		jQuery(this).attr('type', 'text').val( value );
	}).blur(function(){
		if(isIconic) return;
		if(jQuery(this).data('saved')) {
			jQuery(this).data('saved', 0);
			return;
		}
		saveClb(this);
	}).keyup(function(e){
		if(isIconic) return;
		if(e.keyCode == 13) {	// Enter
			saveClb(this);
			jQuery(this).data('saved', 1).trigger('blur');	// We must blur from element after each save in any case
		}
	});
};
/**
 * Standart button item
 */
nbsElement_btn.prototype.beforeSave = function() {
	nbsElement_btn.superclass.beforeSave.apply(this, arguments);
	this._getEditArea().removeAttr('contenteditable');
};
nbsElement_btn.prototype.afterSave = function() {
	nbsElement_btn.superclass.afterSave.apply(this, arguments);
	this._getEditArea().attr('contenteditable', true);
};
nbsElement_btn.prototype._init = function() {
	nbsElement_btn.superclass._init.apply(this, arguments);
	var self = this;
	this._getEditArea().attr('contenteditable', true).blur(function(){
		self._block.contentChanged();
		//_nbsSaveCanvasDelay();
	}).keypress(function(e){
		if(e.keyCode == 13 && window.getSelection) {	// Enter
			document.execCommand('insertHTML', false, '<br>');
			if (typeof e.preventDefault != "undefined") {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }
		}
	});
	if(this.get('customhover-clb')) {

	}
};
nbsElement_btn.prototype._setColor = function(color) {
	this.set('bgcolor', color);
	var bgElements = this.get('bgcolor-elements');
	if(bgElements)
		bgElements = this._$.find(bgElements);
	else
		bgElements = this._$;
	switch(this.get('bgcolor-to')) {
		case 'border':	// Change only borders color
			bgElements.css({
				'border-color': color
			});
			break;
		case 'txt':
			bgElements.css({
				'color': color
			});
			break;
		case 'bg':
		default:
			bgElements.css({
				'background-color': color
			});
			break;
	}
	if(this._haveAdditionBgEl === null) {
		this._haveAdditionBgEl = this._$.find('.nbsAddBgEl');
		if(!this._haveAdditionBgEl.length) {
			this._haveAdditionBgEl = false;
		}
	}
	if(this._haveAdditionBgEl) {
		this._haveAdditionBgEl.css({
			'background-color': color
		});
	}
	if(this.get('bgcolor-clb')) {
		var clbName = this.get('bgcolor-clb');
		if(typeof(this[clbName]) === 'function') {
			this[clbName]( color );
		}
	}
};
/**
 * Icon item
 */
function nbsElement_icon(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuIconExl';
	}
	this.includePostLinks = true;
	this._menuClass = 'nbsElementMenu_icon';
	nbsElement_icon.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_icon, nbsElementBase);
nbsElement_icon.prototype._setColor = function(color) {
	this.set('color', color);
	this._getEditArea().css('color', color);
};
nbsElement_icon.prototype._getLink = function() {
	var $link = this._$.find('a.nbsLink');
	return $link.length ? $link : false;
};
nbsElement_icon.prototype._setLinkAttr = function(attr, val) {
	switch(attr) {
		case 'href':
			if(val) {
				var $link = this._createLink();
				$link.attr(attr, val);
			} else
				this._removeLink();
			break;
		case 'title':
			var $link = this._createLink();
			$link.attr(attr, val);
			break;
		case 'target':
			var $link = this._createLink();
			val ? $link.attr('target', '_blank') : $link.removeAttr('target');
			break;
	}
};
nbsElement_icon.prototype._createLink = function() {
	var $link = this._getLink();
	if(!$link) {
		$link = jQuery('<a class="nbsLink" />').append( this._$.find('.nbsInputShell') ).appendTo( this._$ );
		$link.click(function(e){
			e.preventDefault();
		});
	}
	return $link;
};
nbsElement_icon.prototype._removeLink = function() {
	var $link = this._getLink();
	if($link) {
		this._$.append( $link.find('.nbsInputShell') );
		$link.remove();
	}
};
/**
 * Table column element
 */
function nbsElement_table_col(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuTableColExl';
	}
	if(typeof(this._menuClass) === 'undefined') {
		this._menuClass = 'nbsElementMenu_table_col';
	}
	if(typeof(this._isMovable) === 'undefined') {
		this._isMovable = true;
	}
	this._showMenuEvent = 'hover';
	this._colNum = 0;
	nbsElement_table_col.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_table_col, nbsElementBase);
nbsElement_table_col.prototype._setColor = function(color) {
	if(color) {
		this.set('color', color);
	} else {
		color = this.get('color');
	}
	var enbColor = parseInt(this.get('enb-color'))
	,	block = this.getBlock()
	,	colNum = this._colNum
	,	cssTag = 'col color '+ colNum;
	if(enbColor) {
		block.setTaggedStyle(block.getParam('cell_color_css'), cssTag, {num: colNum, color: color});
	} else {
		block.removeTaggedStyle(cssTag);
	}
	//_nbsSaveCanvas();
};
nbsElement_table_col.prototype._setColNum = function(num) {
	this._colNum = num;
};
nbsElement_table_col.prototype._afterDestroy = function() {
	nbsElement_table_col.superclass._afterDestroy.apply(this, arguments);
	this._block.checkColWidthPerc();
};
nbsElement_table_col.prototype._showSelectBadgeWnd = function() {
	this.hideMenu();
	nbsUtils.showBadgesLibWnd( this );
};
nbsElement_table_col.prototype._disableBadge = function() {
	this._getBadgeHtml().hide();
};
nbsElement_table_col.prototype._setBadge = function(data) {
	if(data) {
		for(var key in data) {
			this.set('badge-'+ key, data[ key ]);
		}
	} else {
		data = this._getBadgeData();
	}
	if(!data) return;
	
	nbsUtils.updateBadgePrevLib( this._getBadgeHtml().show() );
	this.set('enb-badge', 1);
	var $enbBadgeCheck = this._menu.$().find('[name=enb_badge_col]');
	$enbBadgeCheck.attr('checked', 'checked');
	nbsCheckUpdate( $enbBadgeCheck );
};
nbsElement_table_col.prototype._getBadgeData = function() {
	var keys = ['badge_name', 'badge_bg_color', 'badge_txt_color', 'badge_pos']
	,	data = {};
	for(var i = 0; i < keys.length; i++) {
		data[ keys[i] ] = this.get('badge-'+ keys[ i ]);
		if(!data[ keys[i] ])
			return false;
	}
	return data;
};
nbsElement_table_col.prototype._getBadgeHtml = function() {
	var $badge = this._$.find('.nbsColBadge');
	if(!$badge.length) {
		$badge = jQuery('<div class="nbsColBadge"><div class="nbsColBadgeContent"></div></div>').appendTo( this._getEditArea() );
	}
	return $badge;
};
/**
 * Table description column element
 */
function nbsElement_table_col_desc(jqueryHtml, block) {
	this._isMovable = false;
	nbsElement_table_col_desc.superclass.constructor.apply(this, arguments);
	this.refreshHeight();
	var self = this;
	this.getBlock().$().bind('nbsBlockContentChanged', function(){
		self.refreshHeight();
	});
}
extendNbs(nbsElement_table_col_desc, nbsElement_table_col);
nbsElement_table_col_desc.prototype.refreshHeight = function() {
	var sizes = this.getBlock().getMaxColsSizes();
	for(var key in sizes) {
		var $entity = this._$.find(sizes[ key ].sel);
		if($entity && $entity.length) {
			if(key == 'cells' &&  sizes[ key ].height) {
				var cellNum = 0;
				$entity.each(function(){
					if(typeof(sizes[ key ].height[ cellNum ]) !== 'undefined') {
						jQuery(this).height( sizes[ key ].height[ cellNum ] );
					}
					cellNum++;
				});
			} else {
				$entity.height( sizes[ key ].height );
			}
		}
	}
};
nbsElement_table_col_desc.prototype._initMenu = function() {
	nbsElement_table_col_desc.superclass._initMenu.apply(this, arguments);
	// Column description created from usual table column element, with it's menu.
	// But we can't move or remove (we can hide this from block settings) this type of column, so let's just remove it's move handle from menu.
	var $moveHandle = this._menu.$().find('.nbsElMenuMoveHandlerPlace')
	,	$removeBtn = this._menu.$().find('.nbsRemoveElBtn');
	$moveHandle.next('.nbsElMenuBtnDelimiter').remove();
	$moveHandle.remove();
	$removeBtn.prev('.nbsElMenuBtnDelimiter').remove();
	$removeBtn.remove();
	this._menu.$().css('min-width', '130px');
};
/**
 * Table cell element
 */
function nbsElement_table_cell(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuTableCellExl';
	}
	this._menuClass = 'nbsElementMenu_table_cell';
	nbsElement_table_cell.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_table_cell, nbsElementBase);
nbsElement_table_cell.prototype._initMenuClbs = function() {
	nbsElement_table_cell.superclass._initMenuClbs.apply(this, arguments);
	var self = this;
	this._menuClbs['.nbsTypeTxtBtn'] = function() {
		self._replaceElement('txt_cell_item', 'txt');
	};
	this._menuClbs['.nbsTypeImgBtn'] = function() {
		self._replaceElement('img_cell_item', 'img');
	};
	this._menuClbs['.nbsTypeIconBtn'] = function() {
		self._replaceElement('icon_cell_item', 'icon');
	};
};
nbsElement_table_cell.prototype._replaceElement = function(toParamCode, type) {
	var editArea = this._getEditArea()
	,	elementIter = editArea.find('.nbsEl').data('iter-num')
	,	block = this.getBlock();
	// Destroy current element in cell
	block.destroyElementByIterNum( elementIter );
	// Add new one
	editArea.html( block.getParam( toParamCode ) );
	block._initElementsForArea( editArea );
	this.set('type', type);
	this._menu.$().find('[name=type]').removeAttr('checked').filter('[value='+ type+ ']').attr('checked', 'checked');
};
/**
 * Table Cell Icon element
 */
function nbsElement_table_cell_icon(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuTableCellIconExl';
	}
	this._changeable = true;
	nbsElement_table_cell_icon.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_table_cell_icon, nbsElement_icon);
/**
 * Table Cell Image element
 */
function nbsElement_table_cell_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuTableCellImgExl';
	}
	this._changeable = true;
	nbsElement_table_cell_img.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_table_cell_img, nbsElement_img);
/**
 * Table Cell Image element
 */
function nbsElement_table_cell_txt(jqueryHtml, block) {
	this._typeBtns = {
		octo_el_menu_type_txt: {
			text: toeLangNbs('Text')
		,	type: 'txt'
		,	checked: true
		}
	,	octo_el_menu_type_img: {
			text: toeLangNbs('Image / Video')
		,	type: 'img'
		}
	,	octo_el_menu_type_icon: {
			text: toeLangNbs('Icon')
		,	type: 'icon'
		}
	};
	nbsElement_table_cell_txt.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_table_cell_txt, nbsElement_txt);
nbsElement_table_cell_txt.prototype._afterEditorInit = function(editor) {
	var onclickClb = function() {
		
		var $btn = jQuery('#'+ this._id).find('button:first')
		,	$btnsGroupShell = $btn.parents('.mce-container.mce-btn-group:first')
		,	$radio = $btn.find('input[type=radio]')
		,	type = $radio.val();
		
		if(type === 'txt') return;
		
		$btnsGroupShell.find('input[type=radio]').removeAttr('checked');
		$radio.attr('checked') 
			? $radio.removeAttr('checked')
			: $radio.attr('checked', 'checked');
		nbsCheckUpdateArea( $btnsGroupShell );
		// And now - let's make element change
		var element = this.settings._nbsElement;
		element.getBlock().replaceElement(element, type+ '_item_html', type);
	},	onPostRenderClb = function(type, checked) {
		
		var $btnShell = jQuery('#'+ this._id)
		,	$btn = $btnShell.find('button:first')
		,	txt = $btn.html();
		$btn.html('<label><input type="radio" name="type" value="'+ type+ '" '+ (checked ? 'checked' : '')+' />'+ txt+ '</label>');
		nbsInitCustomCheckRadio( $btn );
	};
	for(var btnKey in this._typeBtns) {
		editor.addButton(btnKey, {
			text: this._typeBtns[ btnKey ].text
		,	_nbsType: this._typeBtns[ btnKey ].type
		,	_nbsChecked: this._typeBtns[ btnKey ].checked
		,	_nbsElement: this
		,	classes: 'btn'
		,	onclick: function() {
				jQuery.proxy(onclickClb, this)();
			}
		,	onpostrender: function(e) {
				jQuery.proxy(onPostRenderClb, this)(this.settings._nbsType, this.settings._nbsChecked);
			}
		});
	}
};
nbsElement_table_cell_txt.prototype._beforeInit = function() {
	var btnsPack = [];
	for(var btnKey in this._typeBtns) {
		btnsPack.push( btnKey );
	}
	this._editorToolbarBtns.push( btnsPack );
};
/**
 * Grid column element
 */
function nbsElement_grid_col(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuGridColExl';
	}
	this._menuClass = 'nbsElementMenu_grid_col';
	this._showMenuEvent = 'hover';
	nbsElement_grid_col.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_grid_col, nbsElementBase);
nbsElement_grid_col.prototype._init = function() {
	nbsElement_grid_col.superclass._init.apply(this, arguments);
	var self = this;
	this._$.mouseover(function( event ){
		nbsUtils.getColResizer().moveToElement( self );
		nbsUtils.getRowResizer().moveToElement( self );
	}).mouseout(function( event ){
		nbsUtils.getColResizer().checkHide( event );
		nbsUtils.getRowResizer().checkHide( event );
	});
};
nbsElement_grid_col.prototype._setColor = function(color) {
	if(color) {
		this.set('color', color);
	} else {
		color = this.get('color');
	}
	var enbColor = parseInt(this.get('enb-color'));
	if(enbColor) {
		this._$.css({
			'background-color': color
		}).attr('bgcolor', color);
	} else {
		this._$.css({
			'background-color': 'transparent'
		}).removeAttr('bgcolor');
	}
	_nbsSaveCanvas();
};
nbsElement_grid_col.prototype._setImg = function(imgUrl) {
	if(imgUrl) {
		this.set('bg-img', imgUrl);
	} else {
		imgUrl = this.get('bg-img');
	}
	var enbBgImg = parseInt(this.get('enb-bg-img'));
	if(enbBgImg) {
		this._$.css({
			'background-image': 'url("'+ imgUrl+ '")'
		}).attr('background', imgUrl);
	} else {
		this._$.css({
			'background-image': 'url("")'
		}).removeAttr('background');
	}
	_nbsSaveCanvas();
};
nbsElement_grid_col.prototype._initMenuClbs = function() {
	nbsElement_grid_col.superclass._initMenuClbs.apply(this, arguments);
	var self = this;
	this._menuClbs['.nbsImgChangeBtn'] = function() {
		nbsCallWpMedia({
			id: self._$.attr('id')
		,	clb: function(opts, attach, imgUrl) {
				self._setImg( imgUrl );
			}
		});
	};
	this._menuClbs['.nbsAddColBtn'] = function( menu, htmlEl ) {
		var element = menu.getElement()
		,	addTo = jQuery( htmlEl ).data('to');
		element.getBlock().addCol({
			addTo: addTo
		,	element: element
		});
	};
	this._menuClbs['.nbsAddRowBtn'] = function( menu, htmlEl ) {
		var element = menu.getElement()
		,	addTo = jQuery( htmlEl ).data('to');
		element.getBlock().addRow({
			addTo: addTo
		});
	};
	this._menuClbs['.nbsAddElBtn'] = function( menu, htmlEl ) {
		var colElement = menu.getElement()
		,	elCode = jQuery( htmlEl ).data('code')
		,	$el = jQuery('#nbsAddElementsExamples').find('.nbsAddColElEx[data-el="'+ elCode+ '"]').clone().removeClass('nbsAddColElEx');
		colElement.$().append( $el );
		colElement.getBlock()._initElementsForArea( $el );
	};
	this._menuClbs['.nbsMergeColBtn'] = function( menu, htmlEl ) {
		var element = menu.getElement()
		,	mergeTo = jQuery( htmlEl ).data('to');
		element.getBlock().mergeCols({
			mergeTo: mergeTo
		,	element: element
		});
	};
	this._menuClbs['.nbsAlignColBtn'] = function( menu, htmlEl ) {
		var element = menu.getElement()
		,	alignTo = jQuery( htmlEl ).data('to');
		element.getBlock().alignCol({
			alignTo: alignTo
		,	element: element
		});
	};
};
nbsElement_grid_col.prototype.getColSpan = function() {
	return this._$.attr('colspan');
};
nbsElement_grid_col.prototype.setColSpan = function( val ) {
	this._$.attr('colspan', val);
};
nbsElement_grid_col.prototype.getRowSpan = function() {
	return this._$.attr('rowspan');
};
nbsElement_grid_col.prototype.setRowSpan = function( val ) {
	this._$.attr('rowspan', val);
};
/**
 * Col padding
 */
function nbsElement_col_padd(jqueryHtml, block) {
	nbsElement_col_padd.superclass.constructor.apply(this, arguments);
	this._padd = this.get('padd');
}
extendNbs(nbsElement_col_padd, nbsElementBase);
nbsElement_col_padd.prototype._init = function() {
	nbsElement_col_padd.superclass._init.apply(this, arguments);
	var self = this;
	this._$.mouseover(function( event ){
		nbsUtils.getColResizer().moveToElement( self );
	}).mouseout(function( event ){
		nbsUtils.getColResizer().checkHide( event );
	});
};
/**
 * Row padding
 */
function nbsElement_row_padd(jqueryHtml, block) {
	nbsElement_row_padd.superclass.constructor.apply(this, arguments);
	this._padd = this.get('padd');
}
extendNbs(nbsElement_row_padd, nbsElementBase);
nbsElement_row_padd.prototype._init = function() {
	nbsElement_row_padd.superclass._init.apply(this, arguments);
	var self = this;
	this._$.mouseover(function( event ){
		nbsUtils.getRowResizer().moveToElement( self );
	}).mouseout(function( event ){
		nbsUtils.getRowResizer().checkHide( event );
	});
};
/**
 * Delimiter
 */
function nbsElement_delimiter(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuDelimiterExl';
	}
	this._menuClass = 'nbsElementMenu_delimiter';
	nbsElement_delimiter.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_delimiter, nbsElementBase);
nbsElement_delimiter.prototype._setColor = function(color) {
	this.set('color', color);
	this._$.find('.nbsDelimContent').css('background-color', color);
};
/**
 * Post Dynamic Text element
 */
function nbsElement_dyn_txt(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuDynTxtExl';
	}
	this._menuClass = 'nbsElementDynTxt_menu';
	this._dynProps = {};
	this._editType = '';
	this._isCategory = false;
	this._isWidget = false;
	nbsElement_dyn_txt.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_dyn_txt, nbsElementBase);
nbsElement_dyn_txt.prototype._init = function() {
	nbsElement_dyn_txt.superclass._init.apply(this, arguments);
	this._parseProps();
};
nbsElement_dyn_txt.prototype._parseProps = function() {
	var propsStr = this.get('props');
	if(propsStr) {
		var propsArr = propsStr.split(';');
		for(var i = 0; i < propsArr.length; i++) {
			var attrProp = jQuery.map(propsArr[ i ].split(':'), jQuery.trim);
			if(!attrProp[ 0 ]) continue;
			this._dynProps[ attrProp[ 0 ] ] = attrProp[ 1 ];
		}
	}
};
nbsElement_dyn_txt.prototype.getDynProps = function() {
	return this._dynProps;
};
nbsElement_dyn_txt.prototype._setColor = function(color) {
	this._updateElementsStyles( 'color', color );
};
nbsElement_dyn_txt.prototype._setBgColor = function(color) {
	this._updateElementsStyles( 'background-color', color );
};
nbsElement_dyn_txt.prototype._setFontSize = function(size) {
	this._updateElementsStyles( 'font-size', size );
};
nbsElement_dyn_txt.prototype._setFontItalic = function(italic) {
	this._updateElementsStyles( 'font-italic', italic );
};
nbsElement_dyn_txt.prototype._setFontBold = function(bold) {
	this._updateElementsStyles( 'font-bold', bold );
};
nbsElement_dyn_txt.prototype._updateElementsStyles = function(styleKey, styleVal) {
	var block = this.getBlock()
	,	$elementsToUpdate = block.$().find('[data-el="dyn_txt"][data-props*="'+ styleKey+ ':'+ this._dynProps[ styleKey ]+ '"]') // Just for now, just for now........ ///jQuery('.nbsEl[data-el="dyn_txt"][data-props*="'+ styleKey+ ':'+ this._dynProps[ styleKey ]+ '"]')
	,	self = this;
	$elementsToUpdate = $elementsToUpdate.add( $elementsToUpdate.find('> *:not(.nbsElMenu)') );
	switch(styleKey) {
		case 'font-italic':
			$elementsToUpdate.css('font-style', styleVal ? 'italic' : 'normal');
			break;
		case 'font-bold':
			$elementsToUpdate.css('font-weight', styleVal ? 'bold' : 'normal');
			break;
		default:
			$elementsToUpdate.css(styleKey, styleVal);
			break;
	}
	$elementsToUpdate.each(function(){
		var element = self._block.getElementByHtml( jQuery(this) );
		if(element) {
			switch(styleKey) {
				case 'color':
					element._menu._updateColorPicker( styleVal );
					break;
				case 'font-size':
					element._menu._$fontSizeSliderInput.val( styleVal );
					element._menu._$fontSizeSlider.slider('value', styleVal);
					break;
				case 'font-italic':
					element._menu._switchFontItalicBtn( styleVal );
					break;
				case 'font-bold':
					element._menu._switchFontBoldBtn( styleVal );
					break;
				case 'background-color':
					element._menu._updateBgColorPicker( styleVal );
					break;
			}
			element.set(styleKey, styleVal);
		}
	});
	// Update block properties - to make it work correctly after next server compilation
	block.setParam( this._dynProps[ styleKey ], styleVal );
};
/**
 * Post Dynamic Image element
 */
function nbsElement_dyn_img(jqueryHtml, block) {
	nbsElement_dyn_txt.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_dyn_img, nbsElementBase);
nbsElement_dyn_img.prototype._init = function() {
	nbsElement_dyn_img.superclass._init.apply(this, arguments);
	var self = this;
	this._getImg().load(function(){
		self._setResizable( jQuery(this) );
	});
};
nbsElement_dyn_img.prototype._setResizable = function( $img ) {
	var self = this;
	nbsUtils.setResizable($img, {
		start: function(event, ui) {
			if(ui.originalElement) {
				var maxWidth = ui.originalElement.css('max-width');
				if(maxWidth) {
					// Remove max width if exists for time we will make resizing - so it will not influence for resize process
					ui.originalElement.css({
						'max-width': 'none'
					}).get(0)._nbsMaxWidthExists = true;
					
				}
			}
		}
	,	stop: function(event, ui, width, height) {
			if(ui.originalElement) {
				// Set images width - as it was done after resizing
				if(ui.originalElement.get(0)._nbsMaxWidthExists) {
					ui.originalElement.css({
						'max-width': width
					});
				}
				self.getBlock().setParam('img_width', width);
			}
		}
	});
};
nbsElement_dyn_img.prototype._destroyResizable = function( $img ) {
	nbsUtils.destroyResizable( $img );
};
nbsElement_dyn_img.prototype._getImg = function() {
	return this._$.find('img');
};
nbsElement_dyn_img.prototype.beforeSave = function() {
	nbsElement_dyn_img.superclass.beforeSave.apply(this, arguments);
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	this._destroyResizable( this._getImg() );
};
nbsElement_dyn_img.prototype.afterSave = function() {
	nbsElement_dyn_img.superclass.afterSave.apply(this, arguments);
	this._setResizable( this._getImg() );
};

/**
 * Table cell item
 * Used for graceful table <td> element remove.
 * Recalculate percented width according to remaining cells quantity
 */
function nbsElement_td(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'nbsElMenuTdExl';
	}
	this.includePostLinks = false;
	this._menuClass = 'nbsElementMenu_td';
	nbsElement_td.superclass.constructor.apply(this, arguments);
}
extendNbs(nbsElement_td, nbsElementBase);

nbsElement_td.prototype._initMenuClbs = function() {
	nbsElement_td.superclass._initMenuClbs.apply(this, arguments);
	var self = this;
	this._menuClbs['.nbsRemoveElBtn'] = function() {
		self._destroyAndRecalcRowCellsWidth();
	}
};
/**
 * Destroys itself and all sibling elements with attribute role="separator".
 * Separator destruction policy:
 * - when self is leftmost or rightmost element in row, it destroys all separators until non-separator (role is not "separator")
 *   toward center of table
 * - when self is surrounded by other elements, it destroys all separators in right direction until non-separator.
 * @private
 */
nbsElement_td.prototype._destroyAndRecalcRowCellsWidth = function() {
	if (this._$) {
		var myself = this;
		var percented = [];
		var rowElements = [];
		var fullPercWidth = 0;
		var newPercWidth = 0;
		var selfPos = -1; // index of destroyed element in rowElements
		var block = this._block;
		var $_parent = this._$.parent();
		$_parent.children().each(function(index, element) {
			if (myself._$.get()[0] == element) {
				selfPos = index;
			}
			var width = element.style.width || element.width;
			rowElements.push({
				el: element,
				neighbor: selfPos == -1 ? 'left' : (selfPos == index ? 'self' : 'right')
			});
			var percPos = -1;
			if ((percPos = String(width).indexOf('%')) >= 0) {
				var percWidth = Number(width.substr(0, percPos));
				fullPercWidth += percWidth;
				if (myself._$.get()[0] != element) {
					newPercWidth += percWidth;
					percented.push({ el: element, percents: percWidth });
				}
			}
		});
		// removes dom element but if it is nbsElement uses its destroy method
		var destroyDomElement = function(block, domElement) {
			var nbsEl = block.findElementByDom(domElement);
			if (nbsEl) {
				nbsEl.destroy();
			} else {
				domElement.parentNode.removeChild(domElement);
			}
		};
		// destroy neighbor separators
		if (selfPos != rowElements.length - 1) {
			// destroy toward right until non-separator
			for (var i = selfPos + 1; i < rowElements.length && rowElements[i].el.getAttribute('role') == 'separator'; i++) {
				destroyDomElement(block, rowElements[i].el);
			}
		} else if (selfPos == rowElements.length - 1) {
			// destroy toward left until non-separator
			for (i = selfPos - 1; i >= 0 && rowElements[i].el.getAttribute('role') == 'separator'; i--) {
				destroyDomElement(block, rowElements[i].el);
			}
		}
		this.destroy(function(){
			// recalc percented width and set it
			percented.forEach(function(item){
				var newValue = String(item.percents / newPercWidth * 100) + '%';
				item.el.width = newValue;
				item.el.style.width = newValue;
			});
			// check if there is no elements in parent delete parent also
			if ($_parent.children().length == 0) {
				destroyDomElement(block, $_parent.get()[0]);
			}
		});
	}
};