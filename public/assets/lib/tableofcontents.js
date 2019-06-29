function contentsIndex(options){
	var sel = options.selector;
	var contents_el = options.contents;
	var optional = (typeof options.optional === "undefined") ? "h1,h2,h3,h4,h5,h6" : options.optional;
	var secure_optional;

	//optional 처리. (h1-h6)
	var temp_opts = optional.split(",");
	var temp_opts2 = [];
	for(var i in temp_opts){
		var item = temp_opts[i];
		if(item=="h1"||item=="h2"||item=="h3"
		||item=="h4"||item=="h5"||item=="h6"){
			temp_opts2[temp_opts2.length] = item;
		}
	}
	secure_optional = temp_opts2.join(",");

	//generate name for anchor tag
	$(contents_el).find(secure_optional).each(function(i){
		var a_name = "tocitem-"+$(this).prop("tagName")+"-"+i;
		$(this).attr("data-aname",a_name);
	});

	//clone
	//var temp = $(contents_el).find("h1,h2,h3,h4,h5,h6").clone();
	//var ddd = $(contents_el).find("h1,h2,h3,h4,h5,h6");
	//console.log(ddd);

	var temp = $(contents_el).find(secure_optional).map(function(){
		var tagName = $(this).prop("tagName");
		return $("<" + tagName + "></" + tagName + ">").text($(this).text()).wrapInner("<span></span>").attr("data-aname",$(this).attr("data-aname"));
		//return this;
	});

	//console.log(temp.get());
	$(sel).html(temp.get());

	//ul li 생성
	for(var i=6;i>0;i--){
		$(sel).find("h"+i).each(function(){
			$(this).prev("h"+(i-1)).append($(this).get());
		});
		$(sel).find("h"+(i-1)+">h"+i).each(function(){
			$(this).wrapAll("<ul></ul>").wrap("<li></li>");
		});
	}

	/*
	숫자 를 붙임
	*/
	numbering(sel,1,"");
	function numbering(el,depth,numstr)
	{
		if(depth>6) return;
		if($(el).find("h"+depth).length==0)
		{
			return numbering(el,depth+1,"");
		}
		$(el).find("h"+depth).each(function(i){
			var link = $("<a></a>").attr("href","#"+$(this).attr("data-aname")).text(numstr + (i+1) + ".");
			$(this).prepend(" ");
			$(this).prepend(link);
			return numbering(this,depth+1,numstr + (i+1) + ".");
		});
	}

	//create anchor tag
	$(contents_el).find("h1,h2,h3,h4,h5,h6").each(function(i){
		$(this).prepend("<a name='"+$(this).attr("data-aname")+"'></a>");
		$(this).removeAttr("data-aname");
	});
}