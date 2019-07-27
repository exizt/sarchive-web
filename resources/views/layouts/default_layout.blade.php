<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    @if (!App::environment('local'))
    <title>@yield('title') - 언제나 초심</title>
    @else 
    <title>(local) @yield('title') - 언제나 초심</title>
    @endif
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta-description','')">
    <link rel="shortcut icon" href="/assets/images/shortcut.ico" />
    @yield('meta-custom','')
   
    
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
@stack('style-head')
@stack('script-head')
</head>
<body>
<header>
    <div id="sh-header-nav">
        <nav
            class="navbar navbar-expand-md navbar-dark bg-dark sh-bg-nav">
            <div class="container">
                <a class="navbar-brand" href="/">S아카이브</a>
            </div>
        </nav>
    </div></header>
<div>@yield('content')</div>
<footer><div class="site-layout-footer py-5">
	<div class="container my-5">
		<p class="text-muted">© SH Hong. All rights reserved.</p>
	</div>
</div>
</footer>
</body>
</html>