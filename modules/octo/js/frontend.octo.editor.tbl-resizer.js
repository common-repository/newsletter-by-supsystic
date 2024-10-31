function nbsTblResizer( id, helperId, useCoord, screenId ) {
	this._id = id;
	this._helperId = helperId;
	this._$ = jQuery( '#'+ id );
	this._$helper = jQuery( '#'+ helperId );
	this._captured = false;
	this._start = 0;
	this._isLast = false;
	this._el = null;
	this._useCoord = useCoord;
	this._visible = false;

	this._$screen = jQuery( '#'+ screenId );
	this._$screenTarget = this._$screen.find('.nbsResizeNumTarget');
	this._$screenRelated = this._$screen.find('.nbsResizeNumRelated');
	this._screenVisible = false;
	
	this._paddNum = 0;
	this._lineWidth = 4;
	this._helperWidth = 1;
	
	this._init();
}
nbsTblResizer.prototype._init = function() {
	var self = this;
	this._$.mousedown(function( event ){
		self.capture( event );
	});
	this._$.mouseup(function(){
		self.release();
	});
};
nbsTblResizer.prototype.capture = function( event ) {
	this.release();
	this._captured = true;
	if(this._el) {
		this._el.$().addClass('nbsHover');
		this._el.hideMenu();
	}
	jQuery('*').disableSelection();
	jQuery('body').addClass('nbsDragCaptured');
};
nbsTblResizer.prototype.checkMove = function( event ) {
	if(this._captured) {
		this.move( event );
	}
};
nbsTblResizer.prototype.move = function( event ) {
	
};
nbsTblResizer.prototype.release = function() {
	if(this._captured) {
		this._captured = false;
		if(this._el) {
			this._el.$().removeClass('nbsHover');
			if(toeInArrayNbs(this._el.getCode(), ['col_padd', 'row_padd'])) {
				var setPaddKey = 'padding_'+ this._el._padd;
				this._el.getBlock().setParam( setPaddKey, this._paddNum );
			}
			this._el = null;
		}
		jQuery('*').enableSelection();
		jQuery('body').removeClass('nbsDragCaptured');
		this._$screen.hide();
		this._screenVisible = false;
	}
};
nbsTblResizer.prototype.moveToElement = function( element ) {
	if(nbsUtils.isResizeInProgress()) return false;	// Move progress is in action - for any resizer
	//if(this._captured) return false;	// Move progress is in action
	if(this._el && this._el.getId() == element.getId()) return false;	// Same element already computed
	this._visible = true;
	return true;
};
nbsTblResizer.prototype.checkHide = function( event ) {
	if(!this._visible) return;
	var $relatedTarget = jQuery( event.relatedTarget );
	if(!$relatedTarget || $relatedTarget.attr('id') != this._id) {
		this.hide();
	}
};
nbsTblResizer.prototype.hide = function() {
	if(this._captured) return;	// Move progress is in action
	this.release();
	this._$.hide();
	this._$helper.hide();
	this._el = null;
};
nbsTblResizer.prototype.isCaptured = function() {
	return this._captured;
};
function nbsColResizer() {
	nbsColResizer.superclass.constructor.apply(this, ['nbsColResizer', 'nbsColResizerHelper', 'x', 'nbsColResizerScreen']);
	this._elInitWidth = 0;
	this._initLeft = 0;
	this._initHelperLeft = 0;
	this._$moveCols = null;
	this._$reduceCols = null;
	this._$reduceCol = null;
	this._reduceInitWidth = 0;
	this._parentWidth = 0;
}
extendNbs(nbsColResizer, nbsTblResizer);
nbsColResizer.prototype.capture = function( event ) {
	nbsColResizer.superclass.capture.apply(this, arguments);
	this._start = event.pageX;
	this._$moveCols = jQuery([]);
	this._$reduceCols = jQuery([]);
	//console.log(this._el.getCode());
	var $rows = this._el.getBlock()._getAllRows();
	if(this._el.getCode() != 'col_padd' && $rows && $rows.length > 1) {
		var capturedIndex = this._el.$().index()
		,	reduceIndex = this._$reduceCol.index()
		,	self = this;
		$rows.each(function(){
			self._$moveCols = self._$moveCols.add( jQuery(this).find('.nbsCol:eq('+ capturedIndex+ ')') );
			self._$reduceCols = self._$reduceCols.add( jQuery(this).find('.nbsCol:eq('+ reduceIndex+ ')') );
		});
	} else {
		this._$moveCols = this._$moveCols.add( this._el.$() );
		this._$reduceCols = this._$reduceCols.add( this._$reduceCol );
	}
	var $parent = this._$moveCols.parents('table:first');
	this._parentWidth = $parent.width();
};
nbsColResizer.prototype.move = function( event ) {
	var d = event.pageX - this._start
	,	newWidth = this._elInitWidth
	,	reduceWidth = this._reduceInitWidth;
	if(this._isLast) {
		newWidth -= d;
		reduceWidth += d;
	} else {
		newWidth += d;
		reduceWidth -= d;
	}
	//console.log(newWidth, this._$moveCols, this._$reduceCols);
	var movePerc = (newWidth * 100 / this._parentWidth)
	,	reducePerc = (reduceWidth * 100 / this._parentWidth);
	
	var enbSnap = _nbsGetCanvas().isSnapEnabled();
	
	var grids = 100 / 12	// All width is divided for 12 parts - like in Bootstrap CSS engine
	,	gridSnap = 0.4	// Each snap will continue snapping while you are dragging it to distance +/- 0.4%
	,	gridPiece = movePerc / grids
	,	gridFull = Math.floor( gridPiece )
	,	gridFloat = gridPiece - gridFull
	,	movedWithSnap = false;

	if(gridFloat > gridSnap || !enbSnap) {
		this._$moveCols.attr('width', movePerc+ '%');
		this._$reduceCols.attr('width', reducePerc+ '%');
		
		var widthD = newWidth - this._$moveCols.width();
		if(widthD != 0) {
			if(this._isLast) {
				d += widthD;
			} else {
				d -= widthD;
			}
		}
		movedWithSnap = true;
		this._paddNum = movePerc;
	}
	this._$.css({
		'left': this._initLeft + d
	});
	this._$helper.css({
		'left': this._initHelperLeft + d
	});
	this._$screen.css({
		'left': this._initLeft + d
	});
	if(movedWithSnap || !this._screenVisible) {
		if(this._isLast) {
			this._$screenTarget.html( reducePerc.toFixed(2)+ '%' );
			this._$screenRelated.html( movePerc.toFixed(2)+ '%' );
		} else {
			this._$screenTarget.html( movePerc.toFixed(2)+ '%' );
			this._$screenRelated.html( reducePerc.toFixed(2)+ '%' );
		}
	}
	if( !this._screenVisible ) {
		this._$screen.show();
		this._screenVisible = true;
	}
};
nbsColResizer.prototype.moveToElement = function( element ) {
	if(nbsColResizer.superclass.moveToElement.apply(this, arguments)) {
		var $el = element.$();
	
		if($el.parent().children().length > 1) {
	
			var $next = $el.next();
			this._isLast = $next.length == 0;

			var elementOffset = $el.offset()
			,	elementWidth = $el.width()
			,	elementHeight = $el.height()
			,	padding = 5
			,	height = elementHeight - 2 * padding
			,	top = elementOffset.top + padding;

			this._initLeft = elementOffset.left + (this._isLast ? (padding) : (elementWidth - this._lineWidth - padding));
			this._initHelperLeft = this._initLeft + (this._lineWidth - this._helperWidth) / 2;
			this._$.css({
				'left': this._initLeft
			,	'top': top
			}).height( height ).show();
			this._$helper.css({
				'left': this._initHelperLeft
			}).show();
			this._elInitWidth = elementWidth;
			this._el = element;
			this._$reduceCol = this._isLast ? $el.prev() : $next;
			this._reduceInitWidth = this._$reduceCol.width();

			this._$screen.css({
				'top': top - 30
			});
		}
	}
};
function nbsRowResizer() {
	nbsColResizer.superclass.constructor.apply(this, ['nbsRowResizer', 'nbsRowResizerHelper', 'y', 'nbsRowResizerScreen']);
	this._initTop = 0;
	this._initHelperTop = 0;
	this._elInitHeight = 0;
	this._$moveCols = null;
}
extendNbs(nbsRowResizer, nbsTblResizer);
nbsRowResizer.prototype.capture = function( event ) {
	nbsRowResizer.superclass.capture.apply(this, arguments);
	this._start = event.pageY;
	this._$moveCols = this._el.$();
};
nbsRowResizer.prototype.move = function( event ) {
	var d = event.pageY - this._start
	,	newHeight = this._elInitHeight + d;
	this._$moveCols.attr('height', newHeight);
	
	this._paddNum = newHeight;
	
	var heightD = newHeight - this._$moveCols.height();
	if(heightD != 0) {
		d -= heightD;
	}
	var topHelpers = this._initTop + d;
	this._$.css({
		'top': topHelpers
	});
	this._$helper.css({
		'top': this._initHelperTop + d
	});
	this._$screen.css({
		'top': topHelpers
	});
	this._$screenTarget.html( newHeight+ 'px' );
	if( !this._screenVisible ) {
		this._$screen.show();
		this._screenVisible = true;
	}
};
nbsRowResizer.prototype.moveToElement = function( element ) {
	if(nbsRowResizer.superclass.moveToElement.apply(this, arguments)) {
		var $el = element.$();

		var elementOffset = $el.offset()
		,	elementWidth = $el.width()
		,	elementHeight = $el.height()
		,	padding = 5
		,	left = elementOffset.left + padding
		,	width = elementWidth - 2 * padding;

		this._initTop = elementOffset.top + elementHeight - this._lineWidth - padding;
		this._initHelperTop = this._initTop + (this._lineWidth - this._helperWidth) / 2;
		this._$.css({
			'left': left
		,	'top': this._initTop
		}).width( width ).show();
		this._$helper.css({
			'top': this._initHelperTop
		}).show();
		this._elInitHeight = elementHeight;
		this._el = element;
		this._$screen.css({
			'left': left + width / 2
		});
	}
};
function nbsResizeFrame() {
	this._init();
}
nbsResizeFrame.prototype._init = function() {
	jQuery(document).mousemove(function( event ){
		nbsUtils.getColResizer().checkMove( event );
		nbsUtils.getRowResizer().checkMove( event );
	}).mouseup(function( event ){
		nbsUtils.getColResizer().release();
		nbsUtils.getRowResizer().release();
	});
};