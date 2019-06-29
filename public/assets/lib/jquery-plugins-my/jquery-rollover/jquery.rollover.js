/*! =======================================================
 * jQuery RollOver Plugin
 *
 *       Repo : https://github.com/e2xist/jquery-rollover
 *    Version : 1.0.8
 *     Author : Hong seok-hoon (e2xist)
 *   Requires : jquery 1.9.1 or later
 *   Modified : 2017-03-04
======================================================= */
(function($) {
	//methods
	var methods = {
		rollEvent : function(el,options){
			//process of delegate or live
			if(options.async)
			{
				$(document).on("mouseover",options.selector,function(){
					methods.debug(options,"delegated-mouseover");
					methods.rollEventOver(this,options);
				}).on("mouseout",options.selector,function(){
					methods.rollEventOut(this,options);
				});
			}
			//process of normal
			else
			{
				$(el).on("mouseover",function(){
					methods.debug(options,"mouseover");
					methods.rollEventOver(this,options);
				}).on("mouseout",function(){
					methods.rollEventOut(this,options);
				});
			}
		},
		rollEventOver : function(el,options){
			e = options.change;
			//over = options.over;
			over = (typeof $(el).attr("data-over") !== "undefined") ? $(el).attr("data-over") : options.over;

			var applySuffix = function(src){
				var r;
				var reg = /\.([0-9a-z]+)(?:[\?#]|$)/gi;
				var fileext = src.match(reg);
				if(fileext){
					r = src.replace(reg,options.suffix+fileext);
				}
				return r;
			}


			//클래스 방식
			if(e == "class"){
				$(el).addClass(over);

			//img src 방식
			} else if(e=="src"){
				$(el).attr(options.attr.src, $(el).attr("src"));
				$(el).attr("src", over);
			
			//img src suffix 방식
			} else if(e=="suffix"){
				$(el).attr(options.attr.src, $(el).attr("src"));
				$(el).attr("src", applySuffix($(el).attr("src")));

			//background 방식
			} else if(e=="background"){

				//image 경로만 변경 시
				if((/\.(gif|jpg|jpeg|tiff|png|bmp)$/i).test(over)){
					var background = $(el).css("background-image");
					background = background.replace(/^url\(["']?/, "").replace(/["']?\)$/, "");
					$(el).attr(options.attr.background, background);
					$(el).css("background-image", "url('"+over+"')");
				
				//복합적인 css 적용 대비.
				} else {
					//복합적인 css 적용 대비. ex) url() repeat options
					$(el).attr(options.attr.background, $(el).css("background"));
					$(el).css("background", over);
				}

			//background-position 방식
			} else if(e=="background-position"||e=="position"){
				$(el).css("background-position","0px -"+$(el).css("height"));
			}
		},
		rollEventOut : function(el,options){
			e = options.change;
			over = options.over;
			out = options.out;

			//class 적용
			if(e == "class"){
				$(el).removeClass(over);
				if(out!="none"){
					$(el).addClass(out);
				}

			//img src 방식
			} else if(e=="src"){
				$(el).attr("src",$(el).attr(options.attr.src));

			//img src suffix 방식
			} else if(e=="suffix"){
				$(el).attr("src",$(el).attr(options.attr.src));

			//background 방식
			} else if(e=="background"){
				var background = $(el).attr(options.attr.background);

				//image 경로만 변경 시
				if((/\.(gif|jpg|jpeg|tiff|png|bmp)$/i).test(background)){
					$(el).css("background-image", "url('"+background+"')");

				//복합적인 css 적용 대비.
				} else {
					$(el).css("background", background);
				}

			//background-position 방식
			} else if(e=="background-position"||e=="position"){
				$(el).css("background-position","0px 0px");
			}
		},
		//debug messages
		debug: function(options, message){
			if(window.console && window.console.log && options.debug)
			{
				console.log("[JQueryRollOver] "+message);
			}
		}
	};
	$.extend($.fn, {
		rollOver : function(options)
		{
			//set default options
			var defaults = {
				change : "src",
				over   : "none",
				out    : "none",
				suffix : "_on",
				cursor : true,
				async  : false,
				debug  : false,
				selector : "",
				attr : {
					background : "data-jqro-bg",
					src : "data-src"
				}
			};

			//this.selector 가 가능한 경우 지정 (jquery 2 이하 버전)
			if(typeof this.selector !== "undefined")
			{
				defaults.selector = this.selector;
			}

			// options 파라미터가 object 형태 일 경우에 merge
			if(typeof options === 'object'){
				options = $.extend({}, defaults, options);
			} else {
				options = defaults;
			}

			//--nothing selected
			if(!this.length && options.debug)
			{
				methods.debug(options,"Object is null");
				return false;
			}

			//setting cursor
			if(options.cursor){
				this.css("cursor","pointer");
			}

			//call method
			methods.rollEvent(this,options);

			//return
			return this;
		}//<<rollover
	});
}(jQuery));
