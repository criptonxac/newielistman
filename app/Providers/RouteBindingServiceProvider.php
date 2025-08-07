<?php

namespace App\Providers;

use App\Models\AppTest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RouteBindingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Custom route model binding for Test model
        $this->app['router']->bind('test', function ($value) {
            // First try to find by ID
            if (is_numeric($value)) {
                return AppTest::findOrFail($value);
            }
            
            // Then try to find by slug
            $test = AppTest::where('title', $value)->first();
            
            if (!$test) {
                // If not found, try to decode the slug
                $decodedSlug = urldecode($value);
                $test = AppTest::where('title', $decodedSlug)->first();
                
                if (!$test) {
                    // If still not found, try to find by ID again as a fallback
                    $test = AppTest::find($value);
                    
                    if (!$test) {
                        abort(404, 'Test not found');
                    }
                }
            }
            
            return $test;
        });
    }
}
