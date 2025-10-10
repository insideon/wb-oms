<?php

use App\Jobs\FetchWildberriesOrders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // 15분마다 와일드베리스 주문 수집
        $schedule->job(new FetchWildberriesOrders)->everyFifteenMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
