<?php

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return [
    // Sanctum untuk autentikasi SPA atau token
    'auth:sanctum' => EnsureFrontendRequestsAreStateful::class,

    // Middleware tambahan Laravel
    'throttle' => ThrottleRequests::class,
    'bindings' => SubstituteBindings::class,

    // Spatie Role & Permission
    'role' => RoleMiddleware::class,
    'permission' => PermissionMiddleware::class,
    'role_or_permission' => RoleOrPermissionMiddleware::class,
];
