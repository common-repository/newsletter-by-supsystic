/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('octo_image', function(editor) {
	function showImgDialog() {
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				var imgAttrs = {src: attachment.url}
				,	linkAttrs = {};
				if(props.size && attachment.sizes && attachment.sizes[ props.size ] && attachment.sizes[ props.size ].url) {
					imgAttrs.src = attachment.sizes[ props.size ].url;
					if(attachment.sizes[ props.size ].width) {
						imgAttrs.width = attachment.sizes[ props.size ].width;
					}
					if(attachment.sizes[ props.size ].height) {
						imgAttrs.height = attachment.sizes[ props.size ].height;
					}
				}
				if(attachment.alt) {
					imgAttrs.alt = attachment.alt;
				}
				switch(props.link) {
					case 'file':
						linkAttrs.href =  attachment.url;
						break;
					case 'post':
						linkAttrs.href = attachment.link;
						break;
					case 'custom':
						linkAttrs.href = props.linkUrl;
						break;
				}
				if(attachment.title) {
					if(linkAttrs.href) {
						linkAttrs.title = attachment.title;
					} else {
						imgAttrs.title = attachment.title;
					}
				}
				if(props.align && props.align != 'none') {
					var floatStyle = 'float: '+ props.align+ ';';
					switch(props.align) {
						case 'left':
							floatStyle += 'margin-right: 5px;'
							break;
						case 'right':
							floatStyle += 'margin-left: 5px;'
							break;
						case 'center':
							floatStyle = 'display: block; margin: 5px auto;'
							break;
					}
					if(linkAttrs.href) {
						linkAttrs.style = floatStyle;
					} else {
						imgAttrs.style = floatStyle;
					}
				}
				
				var imgTag = '<img ';
				for(var attrKey in imgAttrs) {
					imgTag += attrKey+ '="'+ imgAttrs[ attrKey ]+ '" ';
				}
				imgTag += '/>';
				if(linkAttrs.href) {
					var linkTag = '<a ';
					for(var attrKey in linkAttrs) {
						linkTag += attrKey+ '="'+ linkAttrs[ attrKey ]+ '" ';
					}
					linkTag += '>';
					imgTag = linkTag+ imgTag+ '</a>';
				}
				editor.execCommand('mceInsertContent', false, imgTag);
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		};
		wp.media.editor.open();
	}

	editor.addButton('octo_image', {
		icon: 'image',
		tooltip: 'Insert image',
		onclick: showImgDialog,
		stateSelector: 'img:not([data-mce-object],[data-mce-placeholder])'
	});

	editor.addCommand('mceImage', showImgDialog);
});
