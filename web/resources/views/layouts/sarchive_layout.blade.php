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
<link rel="shortcut icon" href="/assets/symbol/favicon/favicon-2021.ico" />
<link rel="icon" href="/assets/symbol/favicon/sarchive-favicon-2021-152px-compressed.png" sizes="152x152" />
<link rel="apple-touch-icon" sizes="152x152" href="/assets/symbol/favicon/sarchive-favicon-2021-152px-compressed.png" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" integrity="sha512-b2QcS5SsA8tZodcDtGRELiGv5SaKSk1vDHDaQRda0htPYWZ6046lr3kJ5bAAQdpV2mmA/4v0wQF9MyU6/pDIAg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="/assets/modules/scroll-to-top/scroll-to-top.min.css">
<link rel="stylesheet" href="/assets/css/archive.css">
@stack('layout-styles')
<!-- scripts -->
<script type="module" src="/assets/js/app_mod.js"></script>
<script src="/assets/js/common.js"></script>
<script src="/assets/js/nav.js"></script>
<script src="/assets/modules/scroll-to-top/scroll-to-top.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="/assets/modules/jshotkey/jshotkey.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js" integrity="sha512-X/YkDZyjTf4wyc2Vy16YGCPHwAY8rZJY+POgokZjQB2mhIRFJCckEGc6YyX9eNsPfn0PzThEuNs+uaomE5CO6A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- ## semi modules ## -->
<script src="/assets/js/site-base.js"></script>
@stack('scripts')
</head>
<body @isset($bodyParams) @foreach ($bodyParams as $k => $v) data-{{$k}}="{{$v}}" @endforeach @endisset >
    <header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark" >
        <div class="container-fluid">
            <a id="logo" class="navbar-brand me-2" href="/"></a>
            @isset($layoutParams['archiveId'])
            <a class="navbar-brand" href="/archives/{{$layoutParams['archiveId']}}">
                {{$layoutParams['archiveName'] ?? 'S아카이브'}}
            </a>
            @else
            <a class="navbar-brand" href="/">S아카이브</a>
            @endisset
            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto" id="shh-header-navbar"></ul>
                @isset($layoutParams['archiveId'])
                @if(($layoutMode ?? '') != 'first')
                <form class="d-flex" action="/archives/{{ $layoutParams['archiveId'] }}/search" role="search">
                    <input class="form-control form-control-sm me-2 site-shortcut-key-f" type="search" placeholder="Search" name="q" value="{{ $parameters['q'] ?? ''}}">
                    <button class="btn btn-sm btn-outline-success" type="submit">Search</button>
                </form>
                @endif
                @endisset
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle ps-md-5" href="#"
                            data-bs-toggle="dropdown">{{ Auth::user()->name }}</a>
                        <div class="dropdown-menu dropdown-menu-end">
                        @isset($layoutParams['archiveId'])
                        <a class="dropdown-item site-shortcut-key-n site-shortcut-key-a" href="{{ route('doc.create',['archive'=>$layoutParams['archiveId']]) }}">글쓰기</a>
                        @endisset
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/archives">아카이브 변경</a>
                        <a class="dropdown-item" href="/static/shortcut">단축키 일람</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/admin">설정</a>
                        @if (Route::has('logout'))
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            &nbsp;Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
                        @endif
                    </div>
                    </li>
                </ul>
            </div>
        </div> <!-- div.container -->
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
    <div class="container-fluid text-end">
      <p class="text-muted pt-5 small">© SH Hong. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
