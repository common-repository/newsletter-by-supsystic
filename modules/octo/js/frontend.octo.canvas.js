function nbsCanvas(octoData) {
	var self = this;
	
	this._data = octoData;
	this._$ = jQuery('#nbsCanvas');
	this._$cover = jQuery('#nbsCanvasCover');
	
	this._elMenuAnimationProgress = false;

	if(this.getParam('font_family')) {
		this._setFont( this.getParam('font_family'), true );
	}
	
	this._dsblSnap = getCookieNbs('nbs_dsbl_snap');

	if(!g_nbsEdit) {
		if (! Object.keys(octoData.blocks).length && octoData.params.bg_img.length) {
			this.fitCanvasToScreen();

			jQuery(window).resize(function(){
				self.fitCanvasToScreen();
			});
		}

		var gaTracker = octoData.params.ga_tracking_id;

		if (!nbsOcto.isPreviewMode
			&& typeof gaTracker == 'string'
			&& gaTracker.length) {
			this.loadGoogleAnalytics(gaTracker);
		}
	}
}
nbsCanvas.prototype.setAlMenuAnim = function( animProgress ) {
	this._elMenuAnimationProgress = animProgress;
};
nbsCanvas.prototype.getElMenuAnim = function() {
	return this._elMenuAnimationProgress;
};
nbsCanvas.prototype.loadGoogleAnalytics = function (gaTracker) {
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', gaTracker, 'auto');
	ga('send', 'pageview');
};
nbsCanvas.prototype.fitCanvasToScreen = function () {
	var wndHeight = jQuery(window).height();
	jQuery('#nbsCanvas').height( wndHeight );
}
nbsCanvas.prototype.get = function( key ) {
	return this._data[ key ];
};
nbsCanvas.prototype.set = function( key, value ) {
	this._data[ key ] = value;
};
nbsCanvas.prototype.getParam = function( key ) {
	return (this._data.params && this._data.params[ key ]) 
		? this._data.params[ key ] 
		: false;
};
nbsCanvas.prototype.setParam = function( key, value ) {
	if(!this._data.params)
		return;
	this._data.params[ key ] = value;
};
nbsCanvas.prototype.getRaw = function() {
	return this._$;
};
nbsCanvas.prototype._setFont = function( fontFamily, notLoad ) {
	if (notLoad != true) {
		this._getFontLink().attr({
			'href': 'https://fonts.googleapis.com/css?family='+ encodeURIComponent(fontFamily)
		,	'data-font-family': fontFamily
		});
	}

	this._$.css({
		'font-family': fontFamily
	}).find('td').attr('face', fontFamily).css({
		'font-family': fontFamily
	});

	this.setParam('font_family', fontFamily);
};
nbsCanvas.prototype._getFontLink = function() {
	var $link = this._$.find('link.nbsFont');
	if(!$link.length) {
		$link = jQuery('<link class="nbsFont" rel="stylesheet" type="text/css" href="" />').appendTo( this._$ );
	}
	return $link;
};
nbsCanvas.prototype._setFillColor = function( color, cover ) {
	var attrKey = cover ? 'cover_color' : 'bg_color'
	,	$changeEl = cover ? this._$cover : this._$;
	if(typeof(color) === 'undefined') {
		color = this.getParam( attrKey );
	} else {
		this.setParam( attrKey, color );
	}
	$changeEl.css({
		'background-color': color
	}).attr('bgcolor', color);
};
nbsCanvas.prototype._setCoverFillColor = function( color ) {
	this._setFillColor( color, true );
};
nbsCanvas.prototype._updateFillColorFromColorpicker = function( tinyColor, cover ) {
	this._setFillColor( tinyColor.toHexString(), cover );
};
nbsCanvas.prototype._updateCoverFillColorFromColorpicker = function( tinyColor ) {
	this._updateFillColorFromColorpicker( tinyColor, true );	// yeah:)
};
nbsCanvas.prototype._setBgImg = function( url, cover ) {
	var attrKey = cover ? 'cover_img' : 'bg_img'
	,	$changeEl = cover ? this._$cover : this._$;
	if(typeof(url) === 'undefined') {
		url = this.getParam( attrKey );
	} else {
		this.setParam( attrKey, url );
	}
	if(url) {
		$changeEl.css({
			'background-image': 'url("'+ url+ '")'
		}).attr('background', url);
	} else {
		$changeEl.css({
			'background-image': 'url("")'
		}).removeAttr('background');
	}
};
nbsCanvas.prototype._setCoverImg = function( url ) {
	this._setBgImg( url, true );
};
nbsCanvas.prototype._setBgImgPos = function( pos, cover ) {
	var attrKey = cover ? 'cover_img_pos' : 'bg_img_pos'
	,	$changeEl = cover ? this._$cover : this._$;
	if(typeof(pos) === 'undefined') {
		pos = this.getParam( attrKey );
	} else {
		this.setParam( attrKey, pos );
	}
	switch(pos) {
		case 'stretch':
			$changeEl.css({
				'background-position': 'center center'
			,	'background-repeat': 'no-repeat'
			,	'background-attachment': 'fixed'
			,	'-webkit-background-size': 'cover'
			,	'-moz-background-size': 'cover'
			,	'-o-background-size': 'cover'
			,	'background-size': 'cover'
			});
			break;
		case 'center':
			$changeEl.css({
				'background-position': 'center center'
			,	'background-repeat': 'no-repeat'
			,	'background-attachment': 'scroll'
			,	'-webkit-background-size': 'auto'
			,	'-moz-background-size': 'auto'
			,	'-o-background-size': 'auto'
			,	'background-size': 'auto'
			});
			break;
		case 'tile':
			$changeEl.css({
				'background-position': 'left top'
			,	'background-repeat': 'repeat'
			,	'background-attachment': 'scroll'
			,	'-webkit-background-size': 'auto'
			,	'-moz-background-size': 'auto'
			,	'-o-background-size': 'auto'
			,	'background-size': 'auto'
			});
			break;
	}
};
nbsCanvas.prototype._setCoverImgPos = function( pos ) {
	this._setBgImgPos( pos, true );
};
nbsCanvas.prototype._setBgType = function( type ) {
	switch(type) {
		case 'color':
			this._setFillColor();
			break;
		case 'img':
			this._setBgImg();
			this._setBgImgPos();
			break;
	}
};
nbsCanvas.prototype._setCoverType = function( type ) {
	switch(type) {
		case 'color':
			this._setCoverFillColor();
			break;
		case 'img':
			this._setCoverImg();
			this._setCoverImgPos();
			break;
	}
};
nbsCanvas.prototype._getFaviconTag = function() {
	var $fav = jQuery('link[rel="shortcut icon"]');
	if(!$fav || !$fav.length) {
		$fav = jQuery('<link rel="shortcut icon" href="" type="image/x-icon">').appendTo('head');
	}
	return $fav;
};
nbsCanvas.prototype._setFavImg = function( url ) {
	if(typeof(url) === 'undefined') {
		url = this.getParam('fav_img');
	} else {
		this.setParam('fav_img', url);
	}
	if(url) {
		this._getFaviconTag().attr('href', url);
	} else {
		// We can't just remove it here - favicon wil be still there, because browser desided to do it in this way, sorry ;)
		// So, we just put it 1px transparent img.
		this._getFaviconTag().attr('href', NBS_DATA.onePxImg);
	}
};
nbsCanvas.prototype.setKeywords = function(data) {
	this.setParam('keywords', data);
	this._getKeywordsTag().attr('content', data);
};
nbsCanvas.prototype.setDescription = function(data) {
	this.setParam('description', data);
	this._getDescriptionTag().attr('content', data);
};
nbsCanvas.prototype._getKeywordsTag = function() {
	var $tag = jQuery('meta[name="keywords"]');
	if(!$tag.length) {
		$tag = jQuery('<meta name="keywords">').appendTo('head');
	}
	return $tag;
};
nbsCanvas.prototype._getDescriptionTag = function() {
	var $tag = jQuery('meta[name="description"]');
	if(!$tag.length) {
		$tag = jQuery('<meta name="description">').appendTo('head');
	}
	return $tag;
};
nbsCanvas.prototype.getStandardFontsList = function() {
	return nbsBuildConst.standardFonts;
};
nbsCanvas.prototype.getStandardFontsListAssoc = function() {
	var list = this.getStandardFontsList()
	,	res = {};
	for(var i = 0; i < list.length; i++) {
		res[ list[i] ] = list[ i ];
	}
	return res;
};
nbsCanvas.prototype.setWidth = function( width ) {
	this._data.params.width = width;
	this.updateWidth();
};
nbsCanvas.prototype.setWidthUnits = function( units ) {
	this._data.params.width_units = units;
	this.updateWidth();
};
nbsCanvas.prototype.updateWidth = function() {
	var width = this._data.params.width ? this._data.params.width : nbsBuildConst.defCanvasWidth
	,	widthUnits = this._data.params.width_units ? this._data.params.width_units : nbsBuildConst.defCanvasWidthUnits
	,	widthStr = width+ widthUnits;
	this._$.width( widthStr ).attr('width', widthStr);
};
nbsCanvas.prototype.setDsblSnap = function( disabled ) {
	setCookieNbs('nbs_dsbl_snap', disabled, 999);
	this._dsblSnap = disabled;
};
nbsCanvas.prototype.isSnapEnabled = function() {
	return !this._dsblSnap;
};