/*	flipbook v1.1 <http://www.ezflippr.com/>
 Copyright (c) 2012-2015 NuageLab. All rights reserved.
 See license terms.
 */
var ezflippr_books;
var isFirst = false;

if (typeof ezflippr_books == 'undefined') {
	ezflippr_books = [];
	isFirst = true;
}
ezflippr_books.push({
	'json':ezflippr_json,
	'hmac':ezflippr_hmac
});

(function($){
	if (isFirst) {
		$(document).ready(function () {
			var idx = 0;
			$('.ezflippr-flipbook-container').each(function(){
				// Get data
				var ezflippr_json = ezflippr_books[idx]['json'];
				var ezflippr_hmac = ezflippr_books[idx++]['hmac'];
				var $container = $(this);
				var $content = $(this).children('.ezflippr-flipbook-content');

				// Get book base path
				var bookPath;
				var pluginPath;
				$(this).children('script').each(function(){
					var path = $(this).attr('src');
					if (typeof path != 'string') return;

					if (m = path.match(/^(.*\/)definition.js/)) {
						bookPath = m[1];
					}
					if (m = path.match(/^(.*\/)flipbook-wp.js/)) {
						pluginPath = m[1];
					}
				});

				if ((!bookPath) || (!pluginPath)) {
					return false;
				}

				// Detect if we need to go mobile
				var hasFlash = swfobject.hasFlashPlayerVersion("10.2");
				var mobile = false;
				var ua = navigator.userAgent.toLowerCase();
				if (
					(ua.match(/(iphone|ipod|ipad|android|windows ce|blackberry)/i)) ||
					(ua.match(/windows nt 6\.2.*touch/i))
				) {
					mobile = true;
					hasFlash = false;
				}

				// Assign ID to content DIV
				var content_id;
				var i = idx-1;
				do {
					content_id = 'flipbook-content'+i;
				} while ($('#'+content_id).length > 0);
				$content.attr('id', content_id);

				// Initialize flipbook
				var data = $.parseJSON(ezflippr_json);
				$content.css({'background-color':data.colors.bgcolor});
				if (hasFlash) {
					// Set parameters
					var params = {
						wmode:'transparent',
						allowScriptAccess:'sameDomain',
						menu:false,
						quality:'high',
						allowFullScreen:'true'
					};
					var bgi = $content.css('background-image');
					if ((bgi == '') || (typeof bgi == 'undefined')) {
						params['wmode'] = 'gpu';
						params['bgcolor'] = data.colors.bgcolor;
					}
					params['wmode'] = 'opaque';

					// Set FlashVars
					var flashvars = {
						'basepath': bookPath,
						'definition': bookPath+'definition.bin'
					};

					swfobject.embedSWF(pluginPath+"flipbook.swf", content_id, "100%", "100%", "10.2.0", "assets/expressInstall.swf", flashvars, params);
				} else {
					//if (mobile) {
						$content.children().remove();
						moby($, $content, data, ezflippr_hmac, pluginPath, bookPath);
					//}
				}

			});
		});
	}
})(jQuery);


var moby = function($, $content, data, ezflippr_hmac, ezflippr_basepath_assets, ezflippr_basepath_book) {
	var basepath_assets = 'assets';
	var basepath_book = 'book';
	if (typeof ezflippr_basepath_assets != 'undefined') basepath_assets = ezflippr_basepath_assets;
	if (typeof ezflippr_basepath_book != 'undefined') basepath_book = ezflippr_basepath_book;

	var k = '12wroUsiAGl0Po!CHiacHouniefRoedR8424';
	var currentSlide = null;
	var nextSlide;
	var cont = $('<div></div>');
	$content.css('position','relative');
	$content.append(cont);
	cont.css({
		position: 'relative',
		overflow: 'hidden',
		width:'100%',
		height:'100%'
	});
	var $pages = $('<div class="ezflippr-flipbook-pages"></div>');
	cont.append( $pages );
	var small;
	/*if ('screen' in window) {*/
	if (false) {
		small = Math.min(window.screen.width,window.screen.height) < 700;
	} else {
		var w = window,
			d = document,
			e = d.documentElement,
			g = d.getElementsByTagName('body')[0],
			wi = w.innerWidth || e.clientWidth || g.clientWidth,
			he = w.innerHeight|| e.clientHeight|| g.clientHeight;
		small = Math.min(wi,he) < 600;
	}

	k = k.substr(2,-4);
	var hx = ezflippr_hmac;
	var swipe = $('<img src="'+basepath_assets+'/swipe.png" />')
		.css({
			'position':'absolute',
			'top':'50%',
			'left':data.options.rtl ? '25%' : '60%',
			'width':'10%',
			'z-index':'999',
			'opacity':0
		});

	function setWatermark(){
		if (data.options.watermark) {
			if ($('#watermark').length == 0) {
				$content.append(
					$('<div id="watermark"><a href="http://ezflippr.com/?source=watermark_js" target="_blank"><img src="'+basepath_assets+'/logo.png" style="display:block;" /></a></div>')
				);
			}
			$('#watermark img').css({border:0});
			$('#watermark').css({
				position:'absolute',
				bottom:'10px',
				right:'10px'
			});
			$('#watermark').show();
		}
	}
	setInterval(setWatermark,1000);
	setWatermark();

	var h = String(cjs.hms(ezflippr_json, 'wroUsiAGl0Po!CHiacHouniefRoedRie_ou2Ri8x5e&r5+qIe+rl0ch@&tledlec'));

	var pages = [];
	var loaded = 0;
	var cnt = 0;
	for (var s in data.pages) cnt++;

	for (var z=1;;z++) {
		var i = (data.options.rtl ? (cnt - z)+1 : z);
		if (!(i in data.pages)) break;
		//console.log('z='+z+'; i='+i+'; rtl='+data.options.rtl);

		if ((small) && (data.options.lowsmall)) {
			var img = $('<img />').attr('rel', basepath_book+'/images/'+i+'-low.jpg')
		} else {
			var img = $('<img />').attr('rel', basepath_book+'/images/'+i+'-high.jpg')
		}
		img.bind('load',function(ev){
			loaded++;
		});
		var span = $('<div />').append( img );
		if ('zones' in data.pages[i]) {
			for (var j=0;j<data.pages[i].zones.length;j++) {
				var zone = data.pages[i].zones[j];
				switch (zone.type) {
					case 'link':
						var color = data.colors.link.substr(0,7);
						var alpha = parseInt('0x'+data.colors.link.substr(-2))/128;
						var elm = $('<a/>').css({
							display:'block',
							position:'absolute',
							width:((zone.x2-zone.x1)/data.options.width*100)+"%",
							height:((zone.y2-zone.y1)/data.options.height*100)+"%",
							left:(zone.x1/data.options.width*100)+"%",
							top:(zone.y1/data.options.height*100)+"%",
							backgroundColor:color,
							opacity:alpha,
							cursor:'pointer'
						});
						if ('href' in zone) {
							if (zone.href.match(/page:\/\//i)) {
								elm.attr('rel', zone.href.substr(7,zone.href.length-5));
								elm.click(function(){
									var page_no = parseInt($(this).attr('rel'));
									for (var i=0;i<4;i++) {
										if ($content.find('.page-'+(page_no+i)).length > 0) go($content.find('.page-'+(page_no+i)));
									}
								});
							} else {
								elm.attr('href',zone.href);
								elm.attr('target','_blank');
							}
						}
						span.append(elm);
						break;
					case 'youtube':
						if ('vid' in zone) {
							var elm = $('<iframe class="youtube-player" type="text/html" src="javascript:false" frameborder="0"></iframe>')
								.attr('width', ((zone.x2-zone.x1)/data.options.width*100)+"%")
								.attr('height', ((zone.y2-zone.y1)/data.options.height*100)+"%")
								.css({
									position:'absolute',
									left:(zone.x1/data.options.width*100)+"%",
									top:(zone.y1/data.options.height*100)+"%"
								})
								.attr('src', 'http://www.youtube.com/embed/'+zone.vid);
							span.append(elm);
						}
						break;
				}
			}
		}
		pages.push(span);
	}
	var slides;

	// Create (and hide) info
	$content.append( $('<div class="info"></div>').css({position:'absolute',top:0,left:0}) );
	$content.find('.info').hide();

	k += 'ie_ou2Ri8x5e&r5+qIe+rl0ch@&tledlec'

	// Reposition slides
	function repos(fx) {
		$pages.children().remove();
		var currentSlide_id = (currentSlide != null ? currentSlide.attr('class') : '');

		var landscape;
		if (data.options.forcelandscape) landscape = true;
		else landscape = $(window).width() > $(window).height();

		for (var i=0;i<pages.length;i++) {
			var page = $('<div/>').attr('class', 'page-'+(i+1));
			page.append(pages[i]);
			if ((i != 0) && (i+1 < pages.length) && (landscape)) {
				page.append(pages[i+1]);
				pages[i].css('left',pages[i-1].width());
				i++
			}
			$pages.append(page);
		}
		slides = $pages.children();

		var w = data.options.width;
		var h = data.options.height;
		var nw = cont.width()-40;
		var nh = cont.height()-40;
		var div = landscape ? 2 : 1;
		if ((2*w)/h > cont.width()/cont.height()) {
			h = Math.round(h/w*nw/div);
			w = nw/div;
		} else {
			w = Math.round(w/h*nh);
			h = nh;
		}
		if (w > nw) { h = h/w*nw; w = nw; }
		if (h > nh) { w = w/h*nh; h = nh; }
		console.log(w+' x '+h);
		var xpos = Math.max(0, Math.round($pages.width()/2-(w*div)/2));
		var ypos = Math.round((nh-h)/2);

		$content.find('.info').text(xpos+' / '+nw+' / '+(w*div)+' / '+$pages.width());

		var x = 0;
		var i = 1;
		slides.find('div, img').css({
			width:w+'px',
			height:h+'px'
		});
		slides.each(function(){
			$(this).find('div:first').css({left:xpos+'px',top:ypos+'px'});
			if ($(this).find('div').length > 1) {
				$(this).find('div:last').css({left:(xpos+w)+'px',top:ypos+'px'});
			} else if (landscape) {
				$(this).find('div:first').css({left:(xpos+w/2)+'px',top:ypos+'px'});
			}

			$(this).css({
				'position':'absolute',
				'left':x+'px',
				'top':'0',
				'width':'100%',
				'height':'100%',
				'display':'block'
			});
			x += $pages.width();
		});

		currentSlide = slides.first();
		if (currentSlide_id == '') {
			preload(currentSlide);
			currentSlide.show();
		} else {
			var no = parseInt(currentSlide_id.substr(5,currentSlide_id.length-5));
			var elm = null;
			for (var i=0;i<4;i++) {
				if ($content.find('.page-'+(no+i)).length > 0) {
					elm = $content.find('.page-'+(no+i));
					break;
				}
			}
			if (elm != null) {
				preload(elm);
				go(elm,true);
			}
		}
	}
	if (h != hx) data.options.watermark = true;
	repos();
	function next() {
		var nextElm = currentSlide.next();
		if (nextElm.length == 0) {
			//nextElm = cont.children(':first');
			return;
		}
		go(nextElm);
	}
	function prev() {
		var nextElm = currentSlide.prev();
		if (nextElm.length == 0) {
			//nextElm = cont.children(':last');
			return;
		}
		go(nextElm);
	}
	function go(elm, rightNow) {
		if (nextSlide) return;

		// Stop animations
		cont.stop(false, true);
		slides.stop(false, true);

		// Transition
		nextSlide = null;
		nextSlide = elm;
		var x = elm.offset().left+cont.scrollLeft();
		x = elm.offset().left;
		if (isNaN(x)) x = 0;
		console.log('x = '+x);
		console.log('elm.offset.left = '+elm.offset().left);

		if (cont.scrollLeft() == x) {
			autoUnload();
			preload(nextSlide);
			currentSlide = elm;
			nextSlide = null;
		} else {
			preload(elm);
			if (rightNow) {
				cont.scrollLeft(x);
				currentSlide = elm;
				nextSlide = null;
				autoUnload();
			} else {
				cont.animate({scrollLeft: x},250,null,function(){
					currentSlide = elm;
					nextSlide = null;
					autoUnload();
				});
			}
		}
	}
	function preload(elm, noNeighbors) {
		if (elm.length == 0) return;
		elm.find('img').each(function(){
			$(this).attr('src', $(this).attr('rel') ).show();
		});
		elm.addClass('loaded');

		if (!noNeighbors) {
			preload(elm.prev(), true);
			preload(elm.next(), true);
		}
	}
	function autoUnload() {
		var cu = currentSlide.attr('class');
		var pr = currentSlide.prev();
		if (pr.length > 0) pr = pr.attr('class'); else pr = '';
		var ne = currentSlide.next();
		if (ne.length > 0) ne = ne.attr('class'); else ne = '';
		$pages.find('.loaded').each(function(){
			var id = $(this).attr('class');
			if ((id != cu) && (id != pr) && (id != ne)) {
				$(this).find('img').attr('src','').hide();
				$(this).removeClass('loaded');
			}
		});
	}
	function moveToClosest(dir) {
		var func = 'scrollLeft';
		var prop = 'left';
		var closest = null;
		var distance = null;
		curPos = cont[func]();
		slides.each(function(){
			var pos = parseInt($(this).css(prop));
			if (String(pos) == 'NaN') pos = 0;
			if ((!isNaN(dir)) && (dir > 0) && (pos > curPos)) return;
			if ((!isNaN(dir)) && (dir < 0) && (pos < curPos)) return;
			if ((distance == null) || (Math.abs(pos - curPos) < distance)) {
				distance = Math.abs(pos - curPos);
				closest = $(this);
			}
		});
		console.log(closest);
		if ((closest != null) && (distance > 0)) {
			go(closest);
		}
	}

	// Event handlers
	// Touch event handlers
	var touchContext = {};
	function touchStart(e,target) {
		var func = 'scrollLeft';
		if (e.changedTouches.length > 1) return;
		$(e.changedTouches).each(function(){
			touchContext.originalTouchPosition = this['screenX'];
			touchContext.originalScrollPosition = cont[func]();
			touchContext.started = false;
			clearTimeout(touchContext.timeout);
			touchContext.timeout = setTimeout(function(){
				touchContext.started = true;
			},50);
		});
	}
	function touchEnd(e,target) {
		var func = 'scrollLeft';
		clearTimeout(touchContext.timeout);
		if (e.changedTouches.length > 1) {
			touchContext.started = false;
			return;
		}
		moveToClosest( touchContext.originalScrollPosition-cont[func]() );
	}
	function touchMove(e,target) {
		var func = 'scrollLeft';
		//if(event.originalEvent.targetTouches && event.originalEvent.targetTouches.length > 1) return;
		if (e.changedTouches.length > 1) {
			clearTimeout(touchContext.timeout);
			touchContext.started = false;
			return;
		}
		if (!touchContext.started) return;
		$(e.changedTouches).each(function(){
			e.preventDefault();
			var diff = (touchContext.originalTouchPosition-this['screenX']);
			cont[func](touchContext.originalScrollPosition+diff);
		});
	}
	// Mouse drag event handlers
	var mouseContext = {dragging:false};
	function mouseDown(e,target) {
		var func = 'scrollLeft';
		mouseContext.originalTouchPosition = e['screenX'];
		mouseContext.originalScrollPosition = cont[func]();
		mouseContext.dragging = true;
		e.preventDefault();
	}
	function mouseUp(e,target) {
		var func = 'scrollLeft';
		mouseContext.dragging = false;
		moveToClosest( mouseContext.originalScrollPosition-cont[func]() );
	}
	function mouseMove(e,target) {
		if (!mouseContext.dragging) return;
		var func = 'scrollLeft';
		var diff = (mouseContext.originalTouchPosition-e['screenX']);
		cont[func](mouseContext.originalScrollPosition+diff);
		e.preventDefault();
	}

	// Hook
	$(window).resize(function(){
		repos();
		hookTouch();
	});
	$(document).keydown(function(e){
		if (e.keyCode == 37) {
			prev();
			return false;
		}
		if (e.keyCode == 33) {
			if (data.options.rtl) next(); else prev();
		}
		if (e.keyCode == 39) {
			next();
			return false;
		}
		if (e.keyCode == 34) {
			if (data.options.rtl) prev(); else next();
		}
	});
	function hookTouch() {
		var isTouch = !!('ontouchstart' in window);
		if ( isTouch ) {
			slides.each(function(){
				$(this).get(0).ontouchstart = function(e) { touchStart(e,this); }
				$(this).get(0).ontouchmove = function(e) { touchMove(e,this); }
				$(this).get(0).ontouchend = function(e) { touchEnd(e,this); }
			});
			return true;
		}
		return false;
	}
	if (!hookTouch()) {
		slides.each(function(){
			$(this).mousedown(function(e) { mouseDown(e, $(this)); });
		});
		$(document).mouseup(function(e) { mouseUp(e, $(this)); });
		$(document).mousemove(function(e) { mouseMove(e, $(this)); });
	}

	// Go to last page if RTL
	if (data.options.rtl) {
		go( slides = $pages.children().last(), true );
	}

	// Swipe animation
	setTimeout(function(){
		if (loaded < 2) {
			setTimeout(arguments.callee, 1000);
			return;
		}
		if (swipe != null) {
			$content.append(swipe);
			swipe.animate({opacity:1}, 750, 'swing', function(){

				swipe.animate(
					{
						'left':data.options.rtl ? '60%' : '25%'
					},
					1000,
					'swing',
					function(){
						swipe.animate({opacity:0},750,'swing',function(){swipe.remove(); swipe = null;});
					}
				)
			});
		}
	}, 1000);
}

/*
 CryptoJS v3.1.2
 code.google.com/p/crypto-js
 (c) 2009-2013 by Jeff Mott. All rights reserved.
 code.google.com/p/crypto-js/wiki/License
 */
var cjs=cjs||function(g,l){var e={},d=e.lib={},m=function(){},k=d.Base={extend:function(a){m.prototype=this;var c=new m;a&&c.mixIn(a);c.hasOwnProperty("init")||(c.init=function(){c.$super.init.apply(this,arguments)});c.init.prototype=c;c.$super=this;return c},create:function(){var a=this.extend();a.init.apply(a,arguments);return a},init:function(){},mixIn:function(a){for(var c in a)a.hasOwnProperty(c)&&(this[c]=a[c]);a.hasOwnProperty("toString")&&(this.toString=a.toString)},clone:function(){return this.init.prototype.extend(this)}},
		p=d.WordArray=k.extend({init:function(a,c){a=this.words=a||[];this.sigBytes=c!=l?c:4*a.length},toString:function(a){return(a||n).stringify(this)},concat:function(a){var c=this.words,q=a.words,f=this.sigBytes;a=a.sigBytes;this.clamp();if(f%4)for(var b=0;b<a;b++)c[f+b>>>2]|=(q[b>>>2]>>>24-8*(b%4)&255)<<24-8*((f+b)%4);else if(65535<q.length)for(b=0;b<a;b+=4)c[f+b>>>2]=q[b>>>2];else c.push.apply(c,q);this.sigBytes+=a;return this},clamp:function(){var a=this.words,c=this.sigBytes;a[c>>>2]&=4294967295<<
		32-8*(c%4);a.length=g.ceil(c/4)},clone:function(){var a=k.clone.call(this);a.words=this.words.slice(0);return a},random:function(a){for(var c=[],b=0;b<a;b+=4)c.push(4294967296*g.random()|0);return new p.init(c,a)}}),b=e.enc={},n=b.Hex={stringify:function(a){var c=a.words;a=a.sigBytes;for(var b=[],f=0;f<a;f++){var d=c[f>>>2]>>>24-8*(f%4)&255;b.push((d>>>4).toString(16));b.push((d&15).toString(16))}return b.join("")},parse:function(a){for(var c=a.length,b=[],f=0;f<c;f+=2)b[f>>>3]|=parseInt(a.substr(f,
			2),16)<<24-4*(f%8);return new p.init(b,c/2)}},j=b.Latin1={stringify:function(a){var c=a.words;a=a.sigBytes;for(var b=[],f=0;f<a;f++)b.push(String.fromCharCode(c[f>>>2]>>>24-8*(f%4)&255));return b.join("")},parse:function(a){for(var c=a.length,b=[],f=0;f<c;f++)b[f>>>2]|=(a.charCodeAt(f)&255)<<24-8*(f%4);return new p.init(b,c)}},h=b.Utf8={stringify:function(a){try{return decodeURIComponent(escape(j.stringify(a)))}catch(c){throw Error("Malformed UTF-8 data");}},parse:function(a){return j.parse(unescape(encodeURIComponent(a)))}},
		r=d.BufferedBlockAlgorithm=k.extend({reset:function(){this._data=new p.init;this._nDataBytes=0},_append:function(a){"string"==typeof a&&(a=h.parse(a));this._data.concat(a);this._nDataBytes+=a.sigBytes},_process:function(a){var c=this._data,b=c.words,f=c.sigBytes,d=this.blockSize,e=f/(4*d),e=a?g.ceil(e):g.max((e|0)-this._minBufferSize,0);a=e*d;f=g.min(4*a,f);if(a){for(var k=0;k<a;k+=d)this._doProcessBlock(b,k);k=b.splice(0,a);c.sigBytes-=f}return new p.init(k,f)},clone:function(){var a=k.clone.call(this);
			a._data=this._data.clone();return a},_minBufferSize:0});d.Hasher=r.extend({cfg:k.extend(),init:function(a){this.cfg=this.cfg.extend(a);this.reset()},reset:function(){r.reset.call(this);this._doReset()},update:function(a){this._append(a);this._process();return this},finalize:function(a){a&&this._append(a);return this._doFinalize()},blockSize:16,_createHelper:function(a){return function(b,d){return(new a.init(d)).finalize(b)}},_createHmacHelper:function(a){return function(b,d){return(new s.HMAC.init(a,
		d)).finalize(b)}}});var s=e.algo={};return e}(Math);
(function(){var g=cjs,l=g.lib,e=l.WordArray,d=l.Hasher,m=[],l=g.algo.SHA1=d.extend({_doReset:function(){this._hash=new e.init([1732584193,4023233417,2562383102,271733878,3285377520])},_doProcessBlock:function(d,e){for(var b=this._hash.words,n=b[0],j=b[1],h=b[2],g=b[3],l=b[4],a=0;80>a;a++){if(16>a)m[a]=d[e+a]|0;else{var c=m[a-3]^m[a-8]^m[a-14]^m[a-16];m[a]=c<<1|c>>>31}c=(n<<5|n>>>27)+l+m[a];c=20>a?c+((j&h|~j&g)+1518500249):40>a?c+((j^h^g)+1859775393):60>a?c+((j&h|j&g|h&g)-1894007588):c+((j^h^
g)-899497514);l=g;g=h;h=j<<30|j>>>2;j=n;n=c}b[0]=b[0]+n|0;b[1]=b[1]+j|0;b[2]=b[2]+h|0;b[3]=b[3]+g|0;b[4]=b[4]+l|0},_doFinalize:function(){var d=this._data,e=d.words,b=8*this._nDataBytes,g=8*d.sigBytes;e[g>>>5]|=128<<24-g%32;e[(g+64>>>9<<4)+14]=Math.floor(b/4294967296);e[(g+64>>>9<<4)+15]=b;d.sigBytes=4*e.length;this._process();return this._hash},clone:function(){var e=d.clone.call(this);e._hash=this._hash.clone();return e}});g.SHA1=d._createHelper(l);g.hms=d._createHmacHelper(l)})();
(function(){var g=cjs,l=g.enc.Utf8;g.algo.HMAC=g.lib.Base.extend({init:function(e,d){e=this._hasher=new e.init;"string"==typeof d&&(d=l.parse(d));var g=e.blockSize,k=4*g;d.sigBytes>k&&(d=e.finalize(d));d.clamp();for(var p=this._oKey=d.clone(),b=this._iKey=d.clone(),n=p.words,j=b.words,h=0;h<g;h++)n[h]^=1549556828,j[h]^=909522486;p.sigBytes=b.sigBytes=k;this.reset()},reset:function(){var e=this._hasher;e.reset();e.update(this._iKey)},update:function(e){this._hasher.update(e);return this},finalize:function(e){var d=
	this._hasher;e=d.finalize(e);d.reset();return d.finalize(this._oKey.clone().concat(e))}})})();


// Support functions
function gotoLink(href) {
	var parts = href.split(':');
	switch (parts[0]) {
		case 'mailto':
		default:
			document.location = href;
			break;
		case 'javascript':
			var func = href.replace(/^javascript:/,'');
			if (func.substr(-1) != ';') func += ';';
			eval(func);
			break;
	}
}

function vimeo(i){var e=jQuery(window).width()*.25;var o=jQuery(window).height()*.25;var d=jQuery("<div/>");d.addClass("vidplayer");d.css({position:"absolute",left:0,top:0,width:"100%",height:"100%","background-color":"#000",opacity:.8});var r=jQuery("<div/>");r.addClass("vidplayer inner");r.css({position:"absolute",left:e+"px",top:o+"px",width:jQuery(window).width()-2*e+"px",height:jQuery(window).height()-2*o+"px","background-color":"#000"});var t=jQuery('<iframe src="//player.vimeo.com/video/'+i+'" width="100%" height="100%" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>');r.append(t);var a=jQuery('<img src="assets/close.png" class="vidplayer" />');a.css({position:"absolute",right:"5px",top:"5px",cursor:"pointer"});a.click(function(){jQuery(".vidplayer").remove()});d.click(function(){jQuery(".vidplayer").remove()});jQuery("body").append(d);jQuery("body").append(r);jQuery("body").append(a)}jQuery(window).resize(function(){var i=jQuery(window).width()*.25;var e=jQuery(window).height()*.25;jQuery(".vidplayer.inner").css({width:jQuery(window).width()-2*i+"px",height:jQuery(window).height()-2*e+"px",left:i+"px",top:e+"px"})});