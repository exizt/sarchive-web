/*
 * ! jQuery LayerPopup Plugin 1.0.12
 */
/**
 * This Plugin is layer popup in browser
 * @author Hong seok-hoon (e2xist)
 * @requires jquery 1.8 or later
 * @modify 2015-10-20
 * @description $(document).ready(function(){ 안에 넣어주세요. $(selector).layerPopup();
 * @ex $("#reser_box").layerPopup({background:".backLayer",open:".open_btn"});
 * $("#reser_box").layerPopup({background:".backLayer",open:".open_btn",close:".close_btn"});
 * $("#reser_box").layerPopup({background:".backLayer",close:".close_btn"});
 * $("#reser_box").layerPopup({background:"none",close:".close_btn"});//background no use
 */
(function($) {
	// methods created. private methods
	var methods = {
		//prevent Event
		preventDefault : function(e) {
			if (e.preventDefault)
				e.preventDefault();
			else
				e.returnValue = false;
		},
		// Method for initialize
		init : function($this, options) {
			$this.css({
				"position" : "fixed",
				"z-index": "10000"
			}).hide();

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

				$(options.background).click(function(e) {
					methods.preventDefault(e);
					methods.bgclose($this,options);
				});
			}

			// window resize event
			$(window).resize(function() {
				// checks layer status
				if ($this.css("display") != "none") {
					methods.backLayer_size(options);
					methods.layer_position($this,options);
				}
			});

			// open button click event
			if (options.open != "none") {
				$(options.open).click(function(e) {
					methods.open($this,options);// call layer open
				});
			}

			// open button click event
			if (options.close != "none") {
				$(options.close).click(function(e) {
					methods.preventDefault(e);
					methods.close($this,options);
				});
			}

			// to function esc
			$(document).keydown(function(e) {
				if (e.which == '27') {
					if ($this.css("display") != "none") {
						methods.close($this,options);
					}
				}
			});
			// 속성에 변경된 옵션값을 저장
			$this.data("layerPopup", options);
		},
		// Method for layer open
		open : function($this,options) {
			methods.backLayer_size(options);
			$(options.background).fadeTo(500, 0.7);// background
			methods.layer_position($this,options);
			$this.fadeIn(500);// fade in layer
			options.openevent();
		},
		close : function($this,options) {
			$this.fadeOut(300);
			if (options.background != "none") {
				$(options.background).fadeOut(1000, function() {
					$(options.background).width("0").height("0");
					options.closeevent();
				});
			}
		},
		bgclose : function($this,options) {
			$this.fadeOut(300);
			if (options.background != "none") {
				$(options.background).fadeOut(500, function() {
					$(options.background).width("0").height("0");
					options.closeevent();
				});
			}
		},
		layer_position : function($this,options) {
			// --화면중앙에 레이어셋팅
			if ($this.outerHeight() < $(window).height()) {
				$this.css('top', ($(window).height() - $this.outerHeight()) / 2 + 'px');
			} else
				$this.css('top', '0px');

			if ($this.outerWidth() < $(window).width()) {
				$this.css('left', ($(window).width() - $this.outerWidth()) / 2 + 'px');
			} else
				$this.css('left', '0px');
		},
		backLayer_size : function(options) {
			if (options.background != "none") {
				// document size
				docSize = {
					width : $(document).width(),
					height : $(document).height()
				};
				width = Math.max($(window).width(), docSize.width);
				height = Math.max($(window).height(), docSize.height);
				// width = $(window).width();
				// height = $(window).height();
				$(options.background).width(width).height(height);
				// console.log(width+" - "+height);
			}
		}
	};
	// window document size
	var docSize = {width : 0,height : 0};
	var bgLayerCnt = 0;

	$.extend($.fn, {
		layerPopup : function(methodOrOptions) {
			// 기본 옵션값
			// default options
			var defaults = {
				debug : false,
				background : "new",
				layer : this.selector,
				close : "none",
				open : "none",
				openevent : function() {
				}, // before open event
				closeevent : function() {
				} // close functions
			};
			var options;
			// merge options
			if (typeof methodOrOptions === "object") {
				options = $.extend({}, defaults, methodOrOptions);
				if(options.debug) console.log(options);
			} else {
				options = defaults;
			}

			// --nothing selected;
			if (!this.length && window.console) {
				if(options.debug) console.log("Object is null");
				return false;
			}

			if(methodOrOptions == "open" || methodOrOptions =="close")
			{
				// 저장된 옵션 을 가져온다.
				//get stored option data
				var stored_options = this.data("layerPopup");

				// 저장된 데이터가 없다면 기본값 지정
				// set default option values if there is no saved data
				if (typeof stored_options !== 'object') {
					options = stored_options;
					methods.init($(this), options);
				}
				if (methodOrOptions == "open") {
					// open 이벤트 직접 호출시
					// call method open
					methods.open($(this),options);
				} else if (methodOrOptions == 'close') {
					// close 이벤트 직접 호출시
					// call method close
					methods.close($(this),options);
				}
			} else {
				// 이벤트 호출이 아니라면, init 처리
				// call method init
				methods.init($(this), options);
			}
			return this;
		}// cl of layerpopup
	});
}(jQuery));
