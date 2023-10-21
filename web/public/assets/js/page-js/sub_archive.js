(function () {
    // 버튼까지 포함한 영역의 selector id
    const containerId = "reorderArchive"

    // listGroup에 대한 selector id
    const listGroupId = "archiveListGroup"

    documentReady(function(){
        bindReorderMode()
    });

    /**
     * 아카이브 순서 변경과 관련된 이벤트 바인딩 및 동작
     */
    function bindReorderMode(){
        if( !document.getElementById(listGroupId) ) return

        // '순서변경' 버튼
        document.getElementById("btnOrderEditModeToggle").addEventListener("click", toggleReorderMode )
        // '순서변경 취소' 버튼
        document.getElementById("btnOrderEditModeCancel").addEventListener("click", e => {
            location.reload()
        } )
        // '순서변경 저장' 버튼
        document.getElementById("btnOrderSave").addEventListener("click", saveArchiveSort )

        /**
         * 모드 변경시 변경할 사항에 대한 선택.
         */
        function getVisibleSelector(mode){
            return `#${containerId} *[data-visible="only_${mode}"]`
        }

        /**
         * 목록 중 하나의 아이템에 대한 가장 큰 범위
         * @returns string
         */
        function getListItemSelector(){
            return `#${listGroupId} > a.list-group-item`
        }

        /**
         * '순서변경' 버튼 클릭시.
         */
        function toggleReorderMode(e){
            e.preventDefault()

            // 일반적인 보여지는 부분 처리
            // '순서변경 모드'에서 숨길 것을 hide
            document.querySelectorAll(getVisibleSelector("index")).forEach(e => {
                e.style.display = 'none'
            });
            // '순서변경 모드'에서 숨길 것을 show
            document.querySelectorAll(getVisibleSelector("edit")).forEach(e => {
                e.style.display = 'block'
            });

            // $(".shh-profile-list").attr("href","#");
            document.querySelectorAll(getListItemSelector()).forEach(el => {
                el.href = "#"
            });

            // 상하 버튼 이벤트 바인딩
            document.querySelectorAll(".ar-btn-ordermode-up").forEach(el => {
                el.addEventListener("click", moveUpItem )
            });
            document.querySelectorAll(".ar-btn-ordermode-down").forEach(el => {
                el.addEventListener("click", moveDownItem )
            });

            /**
             * 아이템을 위로 이동
             */
            function moveUpItem(){
                let item = this.closest(getListItemSelector())
                let before_item = item.previousElementSibling;
                if( !! before_item ){
                    before_item.before(item)
                }
            }

            /**
             * 아이템을 아래로 이동
             */
            function moveDownItem(){
                let item = this.closest(getListItemSelector())
                let next_item = item.nextElementSibling;
                if( !! next_item){
                    next_item.after(item)
                }
            }
        }

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
            document.querySelectorAll(getListItemSelector()).forEach((el, index) => {
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
    }
})();
