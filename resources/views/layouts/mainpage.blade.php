<!DOCTYPE html>
<html lang="ko">
<head>
@include('layouts.partials.head-metaclip')
@include('layouts.partials.head-resourceclip')
@stack('style-head')
@stack('script-head')
</head>
<body>
<header>@include('layouts.partials.header')</header>
<div>@yield('content')</div>
<footer>@include('layouts.partials.footer')</footer>
</body>
@include('layouts.modules.analytics')
</html>