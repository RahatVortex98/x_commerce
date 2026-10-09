<?php

namespace App\Providers;
use App\Models\Category;
use Illuminate\Support\Facades\{Cache, View};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    View::composer(['layouts.shop', 'home'], function ($view) {
        $view->with('menu', Cache::remember('menu', 3600, fn () =>
            Category::whereNull('parent_id')->where('is_active', true)
                ->with('children.children')->orderBy('sort')->get()
        ));
    });
}
}
