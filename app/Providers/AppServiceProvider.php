<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        ini_set("memory_limit", "100M");
        ini_set('post_max_size', '50M');
        ini_set('upload_max_filesize', '50M');
        ini_set('max_execution_time', '0');
        ini_set('max_input_time', '-1');
        Schema::defaultStringLength(191);
    }
}
