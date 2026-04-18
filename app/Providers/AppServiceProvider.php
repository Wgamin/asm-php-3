<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'parent_id')) {
            view()->share(
                'categories',
                \App\Models\Category::whereNull('parent_id')->with('children')->get()
            );
        }
    }
}