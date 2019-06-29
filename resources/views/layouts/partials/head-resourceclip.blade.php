<?php //여기는 가능하다면, cdn 리소스를 이용하는 것도 좋음 ?><!-- styles -->
@if (Config::get('my_settings.resources_cdn_enabled'))
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha256-Md8eaeo67OiouuXAi8t/Xpd8t2+IaJezATVTWbZqSOw=" crossorigin="anonymous" />
@else
<link rel="stylesheet" href="/assets/lib/bootstrap/4.1.1/css/bootstrap.min.css">
@endif
<link rel="stylesheet" href="/assets/lib/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/assets/css/bs-callout.css">
<link rel="stylesheet" href="/assets/css/site-base.css">
<link rel="stylesheet" href="/assets/css/site-layouts.css">
<!-- scripts -->
<script src="/assets/lib/jquery/jquery-3.2.1.min.js"></script>
@if (Config::get('my_settings.resources_cdn_enabled'))
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha256-xaF9RpdtRxzwYMWg4ldJoyPWqyDPCRD0Cv7YEEe6Ie8=" crossorigin="anonymous"></script>
@else 
<script src="/assets/lib/bootstrap/required/4.1.1/popper/1.14.3/popper.min.js"></script>
<script src="/assets/lib/bootstrap/4.1.1/js/bootstrap.min.js"></script>
@endif
<script src="/assets/js/site-base.js"></script>
<!-- modules -->
<link rel="stylesheet" href="/assets/modules/scrolltop/scrolltop.css">
<script src="/assets/modules/scrolltop/scrolltop.js"></script>
<link rel="stylesheet" href="/assets/modules/sh-sidenav/sh-sidenav.css">
<script src="/assets/modules/sh-sidenav/sh-sidenav.js"></script>