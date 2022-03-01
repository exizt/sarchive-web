<?php

/*
|--------------------------------------------------------------------------
| 귀찮아서 여기서 상수화하는 것들 (2022-02-09)
|--------------------------------------------------------------------------
*/
// 인증 센터의 DB 커넥션을 내부적으로 가리키는 명칭
if(!defined('G_AUTHDB_CONNECTION_ID')) { define('G_AUTHDB_CONNECTION_ID', 'shared_auth_db'); }

// 스토리지의 퍼블릭 심볼릭 링크 경로
if(!defined('G_STORAGE_PUBLIC_LINK')) { define('G_STORAGE_PUBLIC_LINK', 'd'); }

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
