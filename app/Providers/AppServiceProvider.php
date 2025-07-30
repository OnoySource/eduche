<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
        // Force HTTPS in production
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // Buat symbolic link ke storage jika belum ada
        if (!File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
                Log::info('Symbolic link ke storage berhasil dibuat.');
            } catch (\Exception $e) {
                Log::error('Gagal membuat symbolic link: ' . $e->getMessage());
            }
        }
    }
}
