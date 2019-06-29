$(document).ready(function(){
	$(".sh-event-sidemenu-toggle").on("click",sh_sidemenu_toggle);
	$(".sh-event-sidemenu-toggle").show();
});
function sh_sidemenu_toggle()
{
	var sel_layout_sidenav = ".sh-sidenav";
	var sel_layout_page = ".sh-layout-page-inner";
	var sel_icon = ".sh-event-sidemenu-right";
	if($(sel_layout_sidenav).is(":visible")){
		$(sel_layout_sidenav).hide();
		//$(sel_layout_sidenav).toggle("slide");
		$(sel_layout_page).removeClass("col-lg-9");
		$(sel_layout_page).addClass("col-lg-12");
		$(sel_icon).show();
	} else {
		$(sel_layout_sidenav).show();
		//$(sel_layout_sidenav).toggle("slide");		
		$(sel_layout_page).addClass("col-lg-9");
		$(sel_layout_page).removeClass("col-lg-12");
		$(sel_icon).hide();		
	}
}