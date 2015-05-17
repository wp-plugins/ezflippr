(function($) {
	tinymce.create('tinymce.plugins.ezflippr', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			var self = this;
			self.url = url;

			ed.addButton('flipbook', {
				title : 'Flipbook',
				cmd : 'flipbook',
				image : url + '/../images/favicon.png'
			});

			ed.addCommand('flipbook', function() {
				ed.windowManager.open({
					title: $('#ezflippr-tinymce-flipbook .dialog-title').val(),
					id : 'ezflippr-tinymce-flipbook',
					width: 500,
					height: 270,
					wpDialog: true,
					dummy:false
				}, {
					plugin_url : url
				});
			});

			//replace from shortcode to an placeholder image
			ed.on('BeforeSetcontent', function(event){
				event.content = self.replaceShortcodes( event.content );
			});

			//replace from placeholder image to shortcode
			ed.on('GetContent', function(event){
				event.content = self.restoreShortcodes(event.content);
			});

			$('#ezflippr-cancel').click(function(ev){
				ed.windowManager.close();
			});

			$('#ezflippr-tinymce-flipbook').submit(function(ev){
				ev.preventDefault();

				if ($('#flipbook_id').val() == '') {
					alert($('#ezflippr-error-noflipbook').val());
				} else {
					var shortcode = '[flipbook id="'+$('#flipbook_id').val()+
						'" width="'+$('#flipbook_w').val()+$('#flipbook_w_u').val()+'"'+
						'" height="'+$('#flipbook_h').val()+$('#flipbook_h_u').val()+'"]';
					ed.execCommand('mceInsertContent', 0, shortcode);
					ed.windowManager.close();
				}
			});

			$('#flipbook_w_u, #flipbook_h_u').change(function(){
				var v;
				if ($(this).val() == '%') {
					v = 100;
				} else {
					v = '';
				}
				$(this).parent().children('input[type=number]').attr('max', v);
			}).trigger('change');
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'ezFlippr Buttons',
				author : 'NuageLab',
				authorurl : 'http://ezflippr.com/',
				version : "1.0"
			};
		},

		getAttr: function(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		},

		getAttrSize: function(s, n, d) {
			n = this.getAttr(s, n);
			if (String(n).match(/^[0-9]+$/)) {
				return n+'px';
			} else if (String(n).match(/^[0-9]+%$/)) {
				return n;
			} else {
				return d;
			}
		},

		replaceShortcodes: function(content) {
			var self = this;
			return content.replace( /\[flipbook([^\]]*)\]/g, function(all, attr) {
				var con = '';
				//var placeholder = self.url + '/img/' + self.getAttr(data,'type') + '.jpg';
				var width = self.getAttrSize(attr, 'width', '100%');
				var height = self.getAttrSize(attr, 'width', '500px');
				var id = self.getAttr(attr, 'id');

				var title;
				if (id in ezflippr_books) {
					title = $('#ezflippr-tinymce-flipbook .placeholder-title-template').val();
					title.replace('%title%', ezflippr_books[id]);
				} else {
					title = "(invalid flipbook?)";
				}

				var data = window.encodeURIComponent( attr );
				content = window.encodeURIComponent( con );

				return '<span style="display:block; text-align:center; vertical-align:middle; width:'+width+'; height:'+height+'; line-height:'+height+'; border:1px dashed #ccc;" class="mceItem ezflippr-flipbook" ' +
					'data-ezf-attr="' + data + '" data-ezf-content="'+ con+'" data-mce-resize="false" data-mce-placeholder="1">'+
					title +
					'</span>';
			});
		},

		restoreShortcodes: function(content) {
			var self = this;
			//match any image tag with our class and replace it with the shortcode's content and attributes
			return content.replace( /(?:<p(?: [^>]+)?>)*(<span [^>]+>)[^<]*<\/span>(?:<\/p>)*/g, function( match, image ) {
				var data = self.getAttr( image, 'data-ezf-attr' );
				var con = self.getAttr( image, 'data-ezf-content' );
				if (typeof con == 'undefined') con = '';

				if (data) {
					var out = '<p>[flipbook' + data + ']';
					if (con != '') out += con + '[/flipbook]';
					out += '</p>';
					return out;
				}
				return match;
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ezflippr', tinymce.plugins.ezflippr);
})(jQuery);