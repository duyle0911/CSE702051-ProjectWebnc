<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share $categories cho cả partials.navbar và layouts.app
        View::composer(['partials.navbar', 'layouts.app'], function ($view) {
            $categories = Category::orderBy('category_name')->get();
            $view->with('categories', $categories);
        });
    }

    public function register()
    {
        //
    }
}
