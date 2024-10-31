var g_nbsEdit = false
,	g_nbsBlockFabric = null
,	g_nbsCanvas = null;

jQuery(document).ready(function(){
	_nbsInitFabric();
	_nbsInitCanvas( nbsOcto );
	if(nbsOcto && nbsOcto.blocks && nbsOcto.blocks.length) {
		for(var i = 0; i < nbsOcto.blocks.length; i++) {
			g_nbsBlockFabric.addFromHtml(nbsOcto.blocks[ i ], jQuery('#nbsCanvas .nbsBlock[data-id="'+ nbsOcto.blocks[ i ].id+ '"]'));
		}
	}
});
function _nbsInitFabric() {
	g_nbsBlockFabric = new nbsBlockFabric();
}
function _nbsGetFabric() {
	return g_nbsBlockFabric;
}
function _nbsInitCanvas(octoData) {
	g_nbsCanvas = new nbsCanvas( octoData );
}
function _nbsGetCanvas() {
	return g_nbsCanvas;
}