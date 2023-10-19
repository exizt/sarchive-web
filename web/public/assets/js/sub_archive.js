(function () {
    documentReady(function(){
        // '순서변경' 버튼
        document.getElementById("btnOrderEditModeToggle").addEventListener("click", changeIndexModeOn )
        // '순서변경 취소' 버튼
        document.getElementById("btnOrderEditModeCancel").addEventListener("click", e => {
            e.preventDefault()
            location.reload()
        } )
        // '순서변경 저장' 버튼
        document.getElementById("btnOrderSave").addEventListener("click", saveArchiveSort )
    });

    /**
     * 아카이브 순서 변경사항을 저장
     */
    function saveArchiveSort(e){
        e.preventDefault()
        console.log("save");
        let dataList = [];

        /*
        $(".shh-profile-list").each(function(index){
            var data = {
                id : $(this).data("archiveId"),
                name : $(this).data("name"),
                index : index
            };
            dataList.push(data)
        })
        */
        document.querySelectorAll(".shh-profile-list").forEach((el, index) => {
            // console.log(index)
            let item_id = el.dataset.id
            let item_label = el.dataset.label

            let data = {
                id : item_id,
                name : item_label,
                index : index
            };
            dataList.push(data)
        });


        //console.log(dataList)
        /*
        $.post({
            url: '/archives/updateSort',
            data: {
                'dataList': dataList
            }
        })
        .done(function(data){
            location.reload()
        })*/
        ajaxSave(dataList)

        /**
         * post ajax 전송
         */
        function ajaxSave(dataList){
            axios.post("/archives/updateSort", {
                'dataList': dataList
            }).then(function(response){
                location.reload()
            })
        }
    }

    /**
     * '순서변경' 버튼 클릭시.
     */
    function changeIndexModeOn(e){
        e.preventDefault()
        // $(".shh-ordermode-hide").hide()
        document.querySelectorAll(".shh-ordermode-hide").forEach(e => {
            e.style.display = 'none'
        });
        // $(".shh-ordermode-show").show()
        document.querySelectorAll(".shh-ordermode-show").forEach(e => {
            e.style.display = 'block'
        });
        // $(".shh-profile-list").attr("href","#");
        document.querySelectorAll(".shh-profile-list").forEach(el => {
            el.href = "#"
        });
        // 상하 버튼 이벤트 바인딩
        //$(".shh-btn-mode-up").on("click",onClickMoveUp);
        //$(".shh-btn-mode-down").on("click",onClickMoveDown);
        // 상하 버튼 이벤트 바인딩
        document.querySelectorAll(".ar-btn-ordermode-up").forEach(el => {
            el.addEventListener("click", moveUpItem )
        });
        document.querySelectorAll(".ar-btn-ordermode-down").forEach(el => {
            el.addEventListener("click", moveDownItem )
        });

        function moveUpItem(){
            let item = this.closest(".shh-profile-list")
            let before_item = item.previousElementSibling;
            if( !! before_item ){
                before_item.before(item)
            }
        }

        function moveDownItem(){
            let item = this.closest(".shh-profile-list")
            let next_item = item.nextElementSibling;
            if( !! next_item){
                next_item.after(item)
            }
        }
    }

})();
