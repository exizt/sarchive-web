(function () {
    const articleLikeBtnSelectorId = "saArticleLikeBtn"

    var documentReady = function(f) {
        document.readyState == 'loading' ? document.addEventListener("DOMContentLoaded", f) : f();
    };

    documentReady(function(){
        if( !document.getElementById(articleLikeBtnSelectorId) ) return
        document.getElementById("saArticleLikeBtn").addEventListener("click", doAjax_Like_event )
    });

    /**
     * 맘에 드는 글인 경우에 누르는 버튼. 나중에 목록 조회 조건에 활용할 예정.
     */
    function doAjax_Like_event(e){
        e.preventDefault()
        // var id = $(this).data("document")
        var id = this.dataset.document

        console.log(id)
        // doAjax_Like(id)

        function doAjax_Like(doc_id){
            ajaxPost()
            function ajaxPost(){
                axios.post("/document/ajax_like", {
                    document: doc_id
                }).then(function(response){
                    location.reload()
                })
            }
        }
    }

})();
