/*! =======================================================
 * jQuery inputimage Plugin
 *
 *       Repo : https://github.com/e2xist/jquery-inputimage
 *    Version : 1.0.6
 *     Author : Hong seok-hoon (e2xist)
 *   Requires : jquery 1.8 or later
 *   Modified : 2017-03-02 
======================================================= */
(function($) {
	//methods
	var methods = {
		//prevent Event
		preventDefault: function(e){
			if(e.preventDefault)
				e.preventDefault();
			else 
				e.returnValue = false;
		},
		debug: function(options, message){
			if(window.console && window.console.log && options.debug)
			{
				console.log("[imageInput]"+message);
			}
		},
		getBrowser : function(){
			var Browser = {
				a : navigator.userAgent.toLowerCase(),
				name: navigator.appName.toLowerCase() 
			}
			this.Browser = {
				a       : Browser.a,
				name    : Browser.name,
				ie      : Browser.a.indexOf('msie') != -1,
				ie6     : Browser.a.indexOf('msie 6') != -1,
				ie7     : Browser.a.indexOf('msie 7') != -1,
				ie8     : Browser.a.indexOf('msie 8') != -1,
				opera   : !!window.opera,
				safari  : Browser.a.indexOf('safari') != -1,
				safari3 : Browser.a.indexOf('applewebkit/5') != -1,
				mac     : Browser.a.indexOf('mac') != -1,
				chrome  : Browser.a.indexOf('chrome') != -1,
				firefox : Browser.a.indexOf('firefox') != -1
			}
			return this.Browser;
		},
		init : function(el,options){
			var br = methods.getBrowser();
			if(br.ie6)
			{
				//일반 input type=file 형식으로 전환
				methods.initOld(el,options);
			}
			else if(br.ie)
			{
				//type=file 과 label 혼용
				methods.initWithLabel(el,options);
			}
			else
			{
				//type=file hidden 처리
				//img 태그 활용
				methods.initWithImage(el,options);
			}

			//자동으로 filepath 부분을 생성
			if(!br.ie6){
				if((options.filepath===true) && options.filepath_selector=="none")
				{
					var html = '<span class="'+options.classname.filepath+'"></span>';
					$(el).closest("."+options.classname.wrap).append(html);
				}
			}

			//파일 선택 시, 파일의 이름을 가져온다.
			if(options.filepath===true)
			{
				$(el).change(function(){
					//get file path
					var path = $(this).val().split('\\').pop();
					methods.debug(options,path);

					//display file path
					if(options.filepath_selector=="none")
					{
						var parent = $(this).parent();
						//methods.debug(options,"dd");
						parent.find("."+options.classname.filepath).text(path);
					}
					else 
					{
						if($(options.filepath_selector).is("input"))
						{
							$(options.filepath_selector).val(path);
						} else {
							$(options.filepath_selector).text(path);
						}
					}
				});
			}
		},
		/*
		 * html5 이상, 크롬 브라우저 가능, 
		 */
		initWithImage : function(el,options){
			//원래의 input 은 hidden 처리
			$(el).hide();
			
			//div 객체 생성
			$(el).wrap("<div class='"+options.classname.wrap+"' style='display:inline;'></div>");
			
			var parent_wrap = $(el).closest("."+options.classname.wrap);
			parent_wrap.each(function(){
				//width, height 지정
				var width = (options.width=="inherit") ? $(this).find(el).width() : options.width;
				var height = (options.height=="inherit") ? $(this).find(el).height() : options.height;

				//data-image 처리
				var image_path = "";
				if(typeof $(this).find(el).data("image") !== "undefined"){
					image_path = $(this).find(el).data("image");
				} else {
					image_path = options.image;
				}
				var img_html = '<img src="'+image_path+'" alt="이미지찾기" class="'+options.classname.image+'" style="cursor:pointer" />';
				$(this).prepend($(img_html).width(width).height(height));
			});
			
			//클릭이벤트 구현
			parent_wrap.find("img."+options.classname.image).click(function(e){
				//click method 방지
				methods.preventDefault(e);
				
				//내부에서 다시 parent 를 호출하면. 굳이 each 를 안 해도 된다.
				var parent = $(this).parent();
				parent.find(el).trigger("click");
			}).mouseover(function(){
				$el = $(this).parent().find(el);
				if(options.over !== "none" || typeof $el.data("over") !== "undefined"){
					var over = (typeof $el.data("over") !== "undefined") ? $el.data("over") : options.over;

					$(this).attr("data-src",$(this).attr("src"));
					$(this).attr("src",over);
				}
			}).mouseout(function(){
				$el = $(this).parent().find(el);
				if(options.over !== "none" || typeof $el.data("over") !== "undefined"){
					$(this).attr("src",$(this).attr("data-src"));
				}
			});
		},
		/**
		 * label 을 이용, input 은 안 보이게 처리.
		 * trigger click 이 동작 안되는 ie8 에서 이용.
		 */
		initWithLabel : function(el,options){
			//div 객체 생성
			$(el).wrap("<div class='"+options.classname.wrap+"' style='display:inline;overflow:hidden;'></div>");

			//input 바로 앞에 label 태그 추가
			var parent_wrap = $(el).closest("."+options.classname.wrap);
			parent_wrap.each(function(){
				//width, height 지정
				var width = (options.width=="inherit") ? $(this).find(el).width() : options.width;
				var height = (options.height=="inherit") ? $(this).find(el).height() : options.height;

				//data-image 처리
				var image_path = "";
				if(typeof $(this).find(el).data("image") !== "undefined"){
					image_path = $(this).find(el).data("image");
				} else {
					image_path = options.image;
				}
				//prepend label html
				var label_html = '<label class="'+options.classname.label +'" for="" '
				+ 'style="display:inline-block;cursor:pointer;'
				+ 'background-image:url('+image_path+')">&nbsp;</label>'; 
				$(this).prepend($(label_html).width(width).height(height));
			});

			//input hidden 처리 (ie8 이하에서는 투명하게 처리)
			var input_style = {"opacity":"0","filter":"alpha(opacity:0)","-moz-opacity":"0","width":"0px","height":"0px","position":"absolute","top":"0","left":"0"};
			$(el).css(input_style);

			//label 태그와 input id 연결
			parent_wrap.each(function(idx){
				if($(this).find(el).attr("id") !== undefined)
				{
					var elementKey = $(this).find(el).attr("id");
					$(this).find("label."+options.classname.label).attr("for",elementKey);
				} else {
					var elementKey = "jq_fileinputimage_"+idx;
					$(this).find("label."+options.classname.label).attr("for",elementKey);
					$(this).find(el).attr("id",elementKey);
				}
			});

			//마우스 구현
			parent_wrap.find("label."+options.classname.label).mouseover(function(){
				$el = $(this).parent().find(el);
				if(options.over !== "none" || typeof $el.data("over") !== "undefined"){
					var over = (typeof $el.data("over") !== "undefined") ? $el.data("over") : options.over;

					$(this).attr("data-background",$(this).css("background-image"));
					$(this).css("background-image",'url('+over+')');
				}
			}).mouseout(function(){
				$el = $(this).parent().find(el);
				if(options.over !== "none" || typeof $el.data("over") !== "undefined"){
					$(this).css("background-image",$(this).attr("data-background"));
				}
			});
		},
		// internet explorer lower 6
		initOld : function(el,options){
			//ie6 이전의 경우는 아무런 처리를 하지 않는다. 뭘 어떻게 할 수가 없다.
			return;
		}
	};

	$.extend($.fn, {
		inputimage : function(options)
		{
			//set default options
			var defaults = {
				image    : "none",
				width    : "inherit",
				height   : "inherit",
				over     : "none",
				filepath : false,
				filepath_selector : "none",
				classname : {
					wrap     : "jq-fileinput-image-wrap",
					image    : "jq-fileinput-image",
					label    : "jq-fileinput-image-label",
					filepath : "jq-fileinput-image-filepath"
				},
				debug:true
			};
			
			//merge options
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
			//methods.debug(options,navigator.userAgent);

			//옵션에서의 가로 세로 지정
			if(options.width !="inherit"){
				options.width = parseInt(options.width) + "px";
			}
			if(options.height !="inherit"){
				options.height = parseInt(options.height) + "px";
			}
			//call method init
			methods.init(this,options);

			return this;
		}// cl of inputimage
	});
}(jQuery));
