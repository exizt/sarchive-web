// const { default: Axios } = require("axios");

$(function(){
    initPagination();
});

function getArchiveId(){
    return $("body").data("archive")
}

function getFolderId(){
    return $("body").data("folder")
}

function getBodyParam(keyname, defValue){
    var def = (typeof defValue === "undefined" || defValue == "") ? "" : defValue
    var s = document.body.dataset[keyname]
    return (typeof s === "undefined" || s == "")? def : s
}

var func = {
    exists : function(v){
        if(typeof v === "undefined" || v == null)
            return false;
        else
            return true;
    },
    empty : function(v){
        if(typeof v === "undefined" || v == null || v == "")
            return true;
        else
            return false;
    }
}

/**
 * 상단 header nav를 그리는 함수
 */
function ajaxHeaderNav(){

    var archiveId = getArchiveId()
    if(!func.exists(archiveId)){
        return;
    }

    /*
    $.getJSON("/ajax/header_nav",{
        archive_id : archiveId
    },function(data){
        $.each(data.list, function(i,item){
            var html = '<li class="nav-item"><a class="nav-link" href="/'
            + archiveId + '/archives?board='+item.id+'">'+item.name+'</a></li>'
            $("#shh-header-navbar").append(html)
        })
    })*/

    //console.log("ajaxHeaderNav")
    axios.get("/ajax/header_nav", {
        params : {
            archive_id : archiveId
        }
    }).then(function(response){
        buildHeadNav(response.data)
    })

    function buildHeadNav(data){
        var html = ""
        $.each(data.list, function(i,item){
            var link = `/folders/${item.id}`
            html += buildHtml(link, item.name)
        })
        $("#shh-header-navbar").append(html)

        function buildHtml(link, label){
            var html = `<li class="nav-item">
                    <a class="nav-link" href="${link}">${label}</a>
                </li>`
            return html
        }
    }
}

/**
 * 우측 folder nav를 그리는 함수
 */
function doAjaxFolderList(){
    var archiveId = getArchiveId()
    var folderId = getFolderId()
    var mode = "folder"
    var params = {}

    // folderId 와 archiveId 가 없을 때는 실행하지 않음.
    if(!func.exists(folderId) && !func.exists(archiveId)) return

    if(!func.exists(folderId)){
        mode = "archive"
        params = {archive_id : archiveId}
    } else {
        params = {archive_id : archiveId, folder_id : folderId}
    }

    axios.get("/ajax/folder_nav", {
        params : params
    }).then(function(response){
        var data = response.data

        // 목록 생성
        buildFolderListItem(mode, data)
    })

    /**
     * 하위 폴더 리스트 생성 함수
     * @param string mode 
     * @param object data 
     */
    function buildFolderListItem(mode, data){
        var currentDepth = (mode == "folder") ? data.currentFolder.depth : 0
        var selectorId = "shh-nav-board-list";
        var idPrefix = 'folderNav-item-';

        $.each(data.list, result);
        $("span.arch-js-temp").remove();

        function result(i, item){
            var depth = item.depth - currentDepth
            var nav = document.getElementById(selectorId)
            if(depth == 1){
                nav.innerHTML += buildHtml(item.id, `/folders/${item.id}`,item.name, item.count, depth)
            } else {
                var repeatString = "❯".repeat(depth-1)
                var label = `${repeatString}&nbsp;&nbsp;${item.name}`
                var html = buildHtml(item.id, `/folders/${item.id}`,label, item.count, depth)
                //document.getElementById(`${idPrefix}${item.parent_id}`).nextElementSibling.innerHTML += html
                document.getElementById(`${idPrefix}${item.parent_id}`).insertAdjacentHTML('beforebegin', html);
            }
        }
        function buildHtml(id, link, label, count, depth){
            var html = `<a href="${link}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center sarc-depth-${depth}"
                data-id="${id}" data-label="${label}">
                    ${label}
                    <span class="arch-indexEditMode-hide badge badge-secondary badge-pill">${count}</span>
            </a><span style="display:none" id="${idPrefix}${id}" class="arch-js-temp"></span>`
            return html
        }
    }
}


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

/**
 * folderSelector 호출
 */
function loadFolderSelectorIframe(iframeId, archiveId, options){
    var url = `/folder-selector?archive=${archiveId}`

    if(typeof options !== "undefined"){
        var s = new URLSearchParams(options).toString();
        url += "&"+s
    }
    document.getElementById(iframeId).src = url;
}