$(function() {
	$(document).on('keyup', function(e){
		if(e.altKey && e.shiftKey){
			if(e.keyCode == 83){
				// S key
				trigger($("#site-shortcut-key-s"));
				//$("#site-shortcut-key-s").click();
			} else if(e.keyCode == 69){
				// E key
				trigger($("#site-shortcut-key-e"));
				//$("#site-shortcut-key-e").click();
			}
		}
		function trigger($el){
			var tagName = $el.prop("tagName").toLowerCase();
			if(tagName=="a"){
				var href = $el.attr('href');
			    window.location.href = href;
			} else {
				$el.click();
			}
		}
	});
});