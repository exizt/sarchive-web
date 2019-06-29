<meta charset="utf-8">
@if (!App::environment('local'))
<title>@yield('title') - 언제나 초심</title>
@else 
<title>(local) @yield('title') - 언제나 초심</title>
@endif
<?php if(session('is_desktop_mode')===TRUE){ ?>
<meta name="viewport" content="width=1024" />
<?php } else { ?>
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<?php } ?>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="@yield('meta-description','')">
<link rel="shortcut icon" href="/assets/images/shortcut.ico" />
@yield('meta-custom','')

<!--  OpenGraph -->
<meta property="og:site_name" content="언제나 초심 프로그래밍" >
<meta property="og:type" content="article">
<meta property="og:title" content="@yield('meta-title','')" >
<meta property="og:description" content="@yield('meta-description','')" >
<meta property="og:url" content="@yield('meta-url','')" >
<meta property="og:image" content="" >
<!-- //OpenGraph -->


<!--  twt card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('meta-title','')" >
<meta name="twitter:description" content="@yield('meta-description','')" >
<meta name="twitter:site" content="언제나 초심 프로그래밍">
<meta property="twitter:image" content="" >

<!-- //twt card -->

