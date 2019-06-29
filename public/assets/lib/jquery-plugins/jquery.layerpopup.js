/*
 * ! jQuery LayerPopup Plugin 1.0.11
 */
/**
 * This Plugin is layer popup in browser
 * @author Hong seok-hoon (e2xist)
 * @requires jquery 1.8 or later
 * @modify 2014-05-07
 * @description $(document).ready(function(){ 안에 넣어주세요. $(selector).layerPopup();
 * @ex $("#reser_box").layerPopup({background:".backLayer",open:".open_btn"});
 * $("#reser_box").layerPopup({background:".backLayer",open:".open_btn",close:".close_btn"});
 * $("#reser_box").layerPopup({background:".backLayer",close:".close_btn"});
 * $("#reser_box").layerPopup({background:"none",close:".close_btn"});//background no use
 */
(function($) {
	// methods created
	var methods = {
		// Method for initialize
		init : function(el, options) {
			// appends background layer
			if (options.background == "new") {
				var bg = "LayerPopupBG" + bgLayerCnt;
				$(document.body).append(
						"<div id=\"" + bg
								+ "\" style=\"position:absolute;display:none;width:0;height:0;\">&nbsp;</div>");
				options.background = "#" + bg;
				bgLayerCnt++;
			}

			// bind click events of background layer
			if (options.background != "none") {
				$(options.background).css({
					"position" : "absolute",
					"left" : "0px",
					"top" : "0px",
					"z-index" : "9999",
					"background-color" : "black"
				}).hide();
				$(options.layer).css("z-index", "10000");
				$(options.background).click(function(e) {
					methods.preventDefault(e);
					methods.bgclose(options);
				});
			}

			// window resize event
			$(window).resize(function() {
				// checks layer status
				if ($(options.layer).css("display") != "none") {
					methods.backLayer_size(options);
					methods.layer_position(options);
				}
			});

			// open button click event
			if (options.open != "none") {
				$(options.open).click(function(e) {
					methods.open(options);// call layer open
				});
			}

			// open button click event
			if (options.close != "none") {
				$(options.close).click(function(e) {
					methods.preventDefault(e);
					methods.close(options);
				});
			}

			// to function esc
			$(document).keydown(function(e) {
				if (e.which == '27') {
					if ($(options.layer).css("display") != "none") {
						methods.close(options);
					}
				}
			});
			// 속성에 변경된 옵션값을 저장
			el.data("layerPopup", options);
		},
		// Method for layer open
		open : function(options) {
			methods.backLayer_size(options);
			$(options.background).fadeTo(500, 0.7);// background
			methods.layer_position(options);
			$(options.layer).fadeIn(500);// fade in layer
			options.openevent();
		},
		close : function(options) {
			$(options.layer).fadeOut(300);
			if (options.background != "none") {
				$(options.background).fadeOut(1000, function() {
					$(options.background).width("0").height("0");
					options.closeevent();
				});
			}
		},
		bgclose : function(options) {
			$(options.layer).fadeOut(300);
			if (options.background != "none") {
				$(options.background).fadeOut(500, function() {
					$(options.background).width("0").height("0");
					options.closeevent();
				});
			}
		},
		layer_position : function(options) {
			// --화면중앙에 레이어셋팅
			if ($(options.layer).outerHeight() < $(window).height()) {
				$(options.layer).css('top', ($(window).height() - $(options.layer).outerHeight()) / 2 + 'px');
			} else
				$(options.layer).css('top', '0px');

			if ($(options.layer).outerWidth() < $(window).width()) {
				$(options.layer).css('left', ($(window).width() - $(options.layer).outerWidth()) / 2 + 'px');
			} else
				$(options.layer).css('left', '0px');
		},
		backLayer_size : function(options) {
			if (options.background != "none") {
				width = Math.max($(window).width(), docSize.width);
				height = Math.max($(window).height(), docSize.height);
				// width = $(window).width();
				// height = $(window).height();
				$(options.background).width(width).height(height);
				// console.log(width+" - "+height);
			}
		},
		preventDefault : function(e) {
			if (e.preventDefault)
				e.preventDefault();
			else
				e.returnValue = false;
		}
	};
	// window document size
	var docSize = {
		width : 0,
		height : 0
	};
	var bgLayerCnt = 0;

	$.extend($.fn, {
		layerPopup : function(options) {
			// set default options
			var defaultOptions = {
				debug : false,
				background : "new",
				layer : this.selector,
				close : "none",
				open : "none",
				openevent : function() {
				}, // open functions
				closeevent : function() {
				} // close functions
			};
			var opts = defaultOptions;

			// merge options
			if (typeof options === 'object') {
				$.extend(opts, options);
			}

			// --nothing selected;
			if (!this.length && options.debug && window.console) {
				console.warn("Object is null");
				return false;
			}

			// document size
			docSize = {
				width : $(document).width(),
				height : $(document).height()
			};

			if (options == 'open') {
				// call method open
				opts = this.data("layerPopup");// save option data

				if (typeof opts !== 'object') {
					opts = defaultOptions;
					methods.init(this, opts);
				}
				methods.open(opts);
			} else if (options == 'close') {
				// call method close
				opts = this.data("layerPopup");// save option data
				methods.close(opts);
			} else {
				// call method init
				methods.init(this, opts);
			}
			return this;
		}// <<layerpopup
	});
}(jQuery));
