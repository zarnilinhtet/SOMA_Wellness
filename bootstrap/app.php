<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException; // 

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => RoleMiddleware::class,
        'permission' => PermissionMiddleware::class,
        'role_or_permission' => RoleOrPermissionMiddleware::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    // 💡 Spatie UnauthorizedException ကို ဖမ်းယူ၍ Homepage သို့ Error Message နှင့် Redirect လုပ်ခြင်း
    $exceptions->render(function (UnauthorizedException $e, Request $request) {
        return redirect('/home')->with('error', 'သင့်တွင် ဤနေရာသို့ ဝင်ရောက်ရန် လုပ်ပိုင်ခွင့် (Role) မရှိပါ။');
    });
    })->create();
