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

/**
 * 상단 header nav를 그리는 함수
 */
function ajaxHeaderNav(){
    var archiveId = wrapData(getArchiveId(), 1)

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
    if(typeof folderId === "undefined" || folderId == null){
        mode = "archive"
        params = {archive_id : archiveId}
    } else {
        params = {archive_id : archiveId, folder_id : folderId}
    }

    axios.get("/ajax/folder_nav", {
        params : params
    }).then(function(response){
        var data = response.data
        // 현재 위치 생성
        buildCurrentPaths(mode, data)

        // 목록 생성
        buildFolderListItem(mode, data)

        // only 버튼 생성
        buildCurrentFolderOnly(mode, data)
    })

    /*
    $.getJSON("/ajax/folder_nav", params, function(data){

        // 현재 위치 생성
        buildCurrentPaths(mode, data)

        // 목록 생성
        buildFolderListItem(mode, data)

        // only 버튼 생성
        buildCurrentFolderOnly(mode, data)
    })
    */

    /**
     * 현재 위치 생성
     * @param string mode 
     * @param object data 
     */
    function buildCurrentPaths(mode, data){
        var html = "";

        // 맨 앞 '아카이브' 생성
        html += buildHtml(`/archives/${data.archive.id}`, data.archive.name)

        // 후위 폴더 경로 생성
        if(mode == "folder"){
            $.each(JSON.parse(data.currentFolder.path),function(i,item){
                //console.log(item)
                html += buildHtml(`/folders/${item.id}`, item.text)
            })
        }
        $("#shh-nav-board-path").append(html)


        function buildHtml(link, label){
            var html = `<li class="breadcrumb-item">
            <a href="${link}">${label}</a>
            </li>`
            return html
        }
    }

    /**
     * 하위 폴더 리스트 생성 함수
     * @param string mode 
     * @param object data 
     */
    function buildFolderListItem(mode, data){
        var currentDepth = (mode == "folder") ? data.currentFolder.depth : 0
        var html = "";
        $.each(data.list, function(i,item){
            var depth = item.depth - currentDepth
            //var t_depth = (depth - 1 < 0) ? 0 : depth - 1

            if(depth >3) return
            var repeatString = "❯".repeat(depth-1)
            var label = `${repeatString}&nbsp;&nbsp;${item.name}`
            html += buildHtml(`/folders/${item.id}`,label, item.doc_count, depth)
            buildFolderListItem(item, currentDepth)
        })
        $("#shh-nav-board-list").append(html)

        function buildHtml(link, label, count, depth){
            var html = `<a href="${link}" 
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center shh-navboardlist-depth-${depth}">
                    ${label} 
                    <span class="badge badge-secondary badge-pill">${count}</span>
            </a>`
            return html
        }
    }

    /**
     * 현재 folder에만 해당되는 게시물 보기 버튼
     */
    function buildCurrentFolderOnly(mode, data){
        var selectorId = "js-folderNav-folderOnly"

        var link = ""
        var label = ""
        if(mode == "folder"){
            link = `/folders/${data.currentFolder.id}?only=1`
            label = `${data.currentFolder.name} (only)`
        } else {
            link = `/archives/${data.archive.id}?only=1`
            label = `${data.archive.name} (only)`
        }

        // html 생성
        document.getElementById(selectorId).innerHTML = buildHtml(link, label)
        document.getElementById(selectorId).style.display = ""

        function buildHtml(link, label){
            var html = `<a href="${link}" 
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" >${label}</a>`
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