jQuery(document).ready(function(){
	
});
function nbsInitCaptcha( $elements ) {
	if(typeof(grecaptcha) === 'undefined') {
		return;
	}
	$elements = $elements ? $elements : jQuery(document).find('.g-recaptcha');
	if($elements && $elements.length) {
		$elements.each(function(){
			var $this = jQuery(this);
			if(!$this.data('recaptcha-widget-id')) {
				var dataForInit = {}
				,	elementData = $this.data()
				,	elementId = $this.attr('id');
				if(!elementId) {
					elementId = 'nbsRecaptcha_'+ (Math.floor(Math.random() * 100000));
					$this.attr('id', elementId);
				}
				if(elementData) {
					for(var key in elementData) {
						if(typeof(elementData[ key ]) === 'string') {
							dataForInit[ key ] = elementData[ key ];
						}
					}
				}
				$this.data('recaptcha-widget-id', grecaptcha.render(elementId, dataForInit));
			}
		});
	}
}
