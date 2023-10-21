<script src="/assets/lib/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
    selector:'textarea',
    height:500,
    menubar: false,
    plugins: "code lists advlist",
    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent code codesample',
    forced_root_block : false,
    force_br_newlines : true,
    init_instance_callback: function (editor) {
        editor.on('keyup', function (e) {
            // 사이트에서 이용되는 단축키 기능
            if(typeof shortcutKeyEvent === "function"){
                shortcutKeyEvent(e);
            }
        });
    }
});
</script>
