$(function(){
	initPagination();
});

/**
 * 현재 선택된 상태의 메뉴 를 active 처리
 */
function activeNavMenuItem(sel,value)
{
	$(sel).find(".item-choice").each(function(){
		if($(this).is("[data-item]"))
		{
			var item = $(this).attr("data-item");
			var check_result = false; 
			if(item.indexOf("|") > -1){
				var items = item.split("|");
				for (var k in items)
				{
					if(items[k]==value){
						check_result = true;
					}
				}
			} else {
				check_result = (item==value) ? true: false;
			}
			if(check_result){
				$(this).addClass("active");
			}
		}
	});
}

// paginiation 관련
function initPagination(){
	$(".pagination").each(function(){
		pagination_responsive($(this));
	});
	$(".pagination").addClass("justify-content-center");
}
// pagiation 반응형 보정
function pagination_responsive($paginate){
    $paginate.find(".disabled").each(function(){
		if($(this).text() == "..."){
			$(this).prev().addClass("d-none d-sm-block");
			$(this).next().addClass("d-none d-sm-block");

			if($(this).index()< 6){
				$(this).closest(".pagination").data("dotPrev",true);
			}
			if($(this).index() > 6){
				$(this).closest(".pagination").data("dotNext",true);
			}			
		}
    });

    var a = $(".pagination").data("dotPrev");
    $paginate.find('li.active')
        .prev().addClass('show-mobile');
    $paginate.find('li.active')    
        .next().addClass('show-mobile');

    $paginate.find('li:first-child, li:last-child, li.active')
    	.addClass('show-mobile');

    $paginate.find('li:first-child')
    	.next().addClass('show-mobile');
    $paginate.find('li:last-child')
		.prev().addClass('show-mobile');


    $paginate.find('li').not(".show-mobile").not(".disabled").addClass("d-none d-sm-block");


    var active_index = $paginate.find('li.active').index();

    if($paginate.data("dotPrev")===false){
	    if(active_index==4 || active_index==5 || active_index==6){
	    	var html = "<li class='page-item disabled d-sm-none'><span class='page-link'>...</span></li>";
	    	$paginate.find('li').eq(2).after(html);
	    }
    }

    if($paginate.data("dotNext")===false){
	    if(active_index==9||active_index==8||active_index==7){
		    var html = "<li class='page-item disabled d-sm-none'><span class='page-link'>...</span></li>";
		    $paginate.find('li').eq(11).after(html);
	    }
    }
}