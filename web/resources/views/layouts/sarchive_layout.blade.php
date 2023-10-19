<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>
@env('local') (local) @endenv
@env('staging') (dev) @endenv
@hasSection('title') @yield('title') - @endif S아카이브
</title>
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="shortcut icon" href="/assets/favicon/favicon-2021.ico" />
<link rel="icon" href="/assets/favicon/sarchive-favicon-2021-152px-compressed.png" sizes="152x152" />
<link rel="apple-touch-icon" sizes="152x152" href="/assets/favicon/sarchive-favicon-2021-152px-compressed.png" />
<!-- styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" integrity="sha512-P5MgMn1jBN01asBgU0z60Qk4QxiXo86+wlFahKrsQf37c9cro517WzVSPPV1tDKzhku2iJ2FVgL67wG03SGnNA==" crossorigin="anonymous" />
<link rel="stylesheet" href="/assets/modules/scroll-to-top/scroll-to-top.min.css">
<link rel="stylesheet" href="/assets/css/archive.css">
@stack('layout-styles')
<!-- scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<!-- popper.JS : dropdown of bootstrap 을 위해 필요. (bootstrap 4.0.0 이후로 추가) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js" integrity="sha512-ubuT8Z88WxezgSqf3RLuNi5lmjstiJcyezx34yIU2gAHonIi27Na7atqzUZCOoY4CExaoFumzOsFQ2Ch+I/HCw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js" integrity="sha512-XKa9Hemdy1Ui3KSGgJdgMyYlUg1gM+QhL6cnlyTe2qzMCYm4nAZ1PsVerQzTTXzonUR+dmswHqgJPuwCq1MaAg==" crossorigin="anonymous"></script>
<!-- ## semi modules ## -->
<script src="/assets/modules/scroll-to-top/scroll-to-top.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="/assets/modules/jshotkey/jshotkey.min.js"></script>
<script src="/assets/js/site-base.js"></script>
<script src="/assets/js/common.js"></script>
<script src="/assets/js/nav.js"></script>
<script type="module" src="/assets/js/app_mod.js"></script>
@stack('scripts')
</head>
<body @isset($bodyParams) @foreach ($bodyParams as $k => $v) data-{{$k}}="{{$v}}" @endforeach @endisset >
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a id="logo" class="navbar-brand mr-2" href="/"></a>

      @isset($layoutParams['archiveId'])
      <a class="navbar-brand" href="/archives/{{$layoutParams['archiveId']}}">
          {{$layoutParams['archiveName'] ?? 'S아카이브'}}
      </a>
      @else
      <a class="navbar-brand" href="/">S아카이브</a>
      @endisset
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto" id="shh-header-navbar"></ul>
        @isset($layoutParams['archiveId'])
        @if(($layoutMode ?? '') != 'first')
        <form class="form-inline my-2 my-lg-0" action="/archives/{{ $layoutParams['archiveId'] }}/search">
            <input class="form-control mr-sm-2 site-shortcut-key-f" type="search" placeholder="Search" aria-label="Search" name="q" value="{{ $parameters['q'] ?? ''}}">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
        @endif
        @endisset
        <ul class="navbar-nav">
          <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink_My" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink_My">
              @isset($layoutParams['archiveId'])
              <a class="dropdown-item site-shortcut-key-n site-shortcut-key-a" href="{{ route('doc.create',['archive'=>$layoutParams['archiveId']]) }}">글쓰기</a>
              @endisset
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/archives">아카이브 변경</a>
              <a class="dropdown-item" href="/static/shortcut">단축키 일람</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/admin">설정</a>
              @if (Route::has('logout'))
              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                  class="fas fa-sign-out-alt" aria-hidden="true"></i>&nbsp;Logout</a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
              @endif
          </div>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <div id="layoutBody">
    @if(($layoutMode ?? '') == 'admin')
      <div class="container-fluid mt-4 mb-5">
        <div class="row">
          <div class="col order-md-2">
            @yield('content')
          </div>
          <div class="col-md-3 order-md-1">
              @include('admin.menus',['current'=> ($currentMenu ?? '') ])
          </div>
        </div>
      </div>
    @else
      @yield('content')
    @endif
  </div>
  <footer>
    <div class="container-fluid text-right">
      <p class="text-muted pt-5">© SH Hong. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
