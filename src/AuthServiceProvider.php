<?php

namespace Abdullahhafizh\Auth;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(PermissionRegistrar $permissionLoader, Filesystem $filesystem)
    {
        if (function_exists('app')) {
            $this->publishes([
                __DIR__.'/Controllers/Controller.php' => app('Controllers/Controller.php'),
            ], 'Controllers');
        }
        else if(function_exists('base_path')) {
            $this->publishes([
                __DIR__.'/Controllers/Controller.php' => base_path('app/Controllers/Controller.php'),
            ], 'controller');
        }
        else {
            $this->publishes([
                __DIR__.'/Controllers/Controller.php' => app()->path('Controllers/Controller.php'),
            ], 'controller');
        }
    }
}