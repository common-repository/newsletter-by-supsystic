function _nbsMainToolbar() {
	this._$ = jQuery('#nbsMainTopBar');
	this._$subBar = jQuery('#nbsMainTopSubBar');
	this._$moreShell = jQuery('#nbsMainOctoOptMore');
	this._subBarVisible = false;
	this._init();
	if(window._octLoaded)
		this.refresh();
}
_nbsMainToolbar.prototype._init = function() {
	var self = this;
	this._$.find('.nbsMainTopBarCenter').show();	// it is hidden until whole page will be loaded because elements there is unsorted
	this._$.find('#nbsMainOctoOptMoreBtn').click(function(){
		if(self._subBarVisible) {
			self._hideSubBar();
		} else {
			self._showSubBar();
		}
		return false;
	});
};
_nbsMainToolbar.prototype._showSubBar = function() {
	if(!this._subBarVisible) {
		this._$subBar.removeClass('flipOutX').addClass('active flipInX');
		this._$.find('#nbsMainOctoOptMoreBtn').addClass('active');
		this._subBarVisible = true;
	}
	return this;
};
_nbsMainToolbar.prototype._hideSubBar = function() {
	if(this._subBarVisible) {
		this._$subBar.removeClass('active flipInX').addClass('flipOutX');
		this._$.find('#nbsMainOctoOptMoreBtn').removeClass('active');
		this._subBarVisible = false;
	}
	return this;
};
_nbsMainToolbar.prototype.refresh = function(params) {
	params = params || {};
	var optsWidgetWidth = this._$.width() - this._$.find('.nbsMainTopBarLeft').outerWidth() - this._$.find('.nbsMainTopBarRight').outerWidth()
	,	optsTotalWidth = this._$.find('.nbsMainTopBarCenter').width()
	,	dMargin = 10;
	if(optsWidgetWidth < optsTotalWidth && !params.recursiveBackToMain) {	// Move main elements - to sub panel when in main panel there are no place for them
		var $lastElementInSet = this._$.find('.nbsMainOctoOpt:not(#nbsMainOctoOptMore):last');
		if($lastElementInSet && $lastElementInSet.length) {
			$lastElementInSet.get(0)._octWidth = $lastElementInSet.width();	// remember it's width for case when we will need it - but element can be hidden
			$lastElementInSet.prependTo( this._$subBar );
			this._$moreShell.show();
			this.refresh({recursiveToSub: true});
		}
	} else {	// Check if we can move some elements - back to main panel
		var $firstOptInSub = this._$subBar.find('.nbsMainOctoOpt:first');
		if($firstOptInSub && $firstOptInSub.length) {
			if(optsWidgetWidth > optsTotalWidth + $firstOptInSub.get(0)._octWidth + dMargin) {
				$firstOptInSub.insertBefore( this._$moreShell );
				this.refresh({recursiveBackToMain: true});
			}
		} else {
			this._$moreShell.hide();
			this._hideSubBar();
		}
	}
	return this;
};
_nbsMainToolbar.prototype._getAllOptsShells = function() {
	var $shells = this._$.find('.nbsMainOctoOpt:not(#nbsMainOctoOptMore)');
	return $shells;
};