<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\VendorContract;
use App\Repositories\VendorRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Collection;
use App\Models\Category;
use App\Models\SocialMedia;
use App\Models\Setting;
use App\Models\User;
use App\Models\State;
use App\Models\Notification;
use App\Models\City;
use Illuminate\Support\Str;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

    
    public function register()
    {
        $this->app->bind(VendorContract::class, VendorRepository::class);
    }
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        view::composer('*', function($view) {
            // Service
            // $ServiceTableExists = Schema::hasTable('products');
            // if ($ServiceTableExists) {
            //     $products = Product::groupBy('title')->pluck('title')->toArray();
            // }
            // // Collection
            // $CollectionTableExists = Schema::hasTable('collections');
            // if ($CollectionTableExists) {
            //     $collections = Collection::groupBy('title')->pluck('title')->toArray();
            // }
            // // Category
            // $CategoryTableExists = Schema::hasTable('categories');
            // if ($CategoryTableExists) {
            //     $categories = Category::groupBy('title')->pluck('title')->toArray();
            // }
            // Category
            $UserTableExists = Schema::hasTable('users');
            if ($UserTableExists) {
                $userCities = User::whereNotNull('city')->groupBy('city')->pluck('city')->toArray();
                $User_state = User::whereNotNull('state')->groupBy('state')->pluck('state')->toArray();
                $cityNames = [];
                foreach ($userCities as $cityId) {
                    $city = City::find($cityId);
                    if ($city) {
                        $cityNames[] = $city->name;
                    }
                }
                $stateNames = [];
                foreach ($User_state as $stateId) {
                    $state = State::find($stateId);
                    if ($state) {
                        $stateNames[] = $state->name;
                    }
                }
                
            }
            // Fetch the social media data
        $socialmedias = SocialMedia::orderBy('title', 'ASC')->paginate(20);
         // Fetch the settings data
        $settings = Setting::pluck('content','title')->toArray();
        // Fetch the Categories data
        $category = Collection::orderBy('title','ASC')->where('status',1)->limit(16)->get();
        // Share the data with the 'front.layout.app' view
        View::composer('front.layout.app', function ($view) use ($socialmedias,$settings,$category) {
            $view->with('socialmedias', $socialmedias);
            $view->with('settings',$settings);
            $view->with('categories',$category);
        });

            $allLocation = array_merge($cityNames, $stateNames);
            // $allTitles = array_merge($products, $collections, $categories);

               // Notification count
               $wishlistExists = Schema::hasTable('notifications');
               if ($wishlistExists) {
                   if (Auth::guard('web')->check()) {
                       $user_id = Auth::guard('web')->user()->id;
                       $notificationCount = Notification::where('seller_id', $user_id)->where('read_at', 0)->count();
                   } else {
                       $notificationCount = 0;
                   }
               }
               // Notification data
               $wishlistExists = Schema::hasTable('notifications');
               if ($wishlistExists) {
                   if (Auth::guard('web')->check()) {
                       $user_id = Auth::guard('web')->user()->id;
                       $notificationData = Notification::where('seller_id', $user_id)->latest('id')->where('read_at', 0)->take(5)->get();
                   } else {
                       $notificationData = collect();
                   }
               }
            view()->share('global_filter_location', $allLocation);
            // view()->share('global_filter_data', $allTitles);
            view()->share('notificationCount', $notificationCount);
            view()->share('notificationData', $notificationData);

        });
    }
}