<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>
@env('local') (local) @endenv
@env('staging') (dev) @endenv
S아카이브
</title>
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-title" content="S아카이브">
<link rel="shortcut icon" href="/assets/favicon/favicon-2021.ico" />
<link rel="icon" href="/assets/favicon/sarchive-favicon-2021-152px-compressed.png" sizes="152x152" />
<link rel="apple-touch-icon" sizes="152x152" href="/assets/favicon/sarchive-favicon-2021-152px-compressed.png" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/darkly/bootstrap.min.css" integrity="sha512-A/WvCV75maYUI3F3yjeSqYg0dUIepPRx14Qw8EZjJ/udG5/s3uDWLHnm1FSbYzrJg4RLdAdEm/f6+1V6AxCBJQ==" crossorigin="anonymous" />
<link rel="stylesheet" href="/assets/css/archive.css">
</head>
<body>
  <header>
    <nav class="bg-dark navbar-dark text-center text-md-left pl-3">
      <a class="navbar-brand" href="/">
        <span id="logo" class="navbar-brand mr-2 d-inline-block align-middle">&nbsp;</span>
        <span class="navbar-brand">S아카이브</span>
      </a>
    </nav>
  </header>
  <div style="margin-top: 10rem;">
    @yield('content')
  </div>
</body>
</html>
