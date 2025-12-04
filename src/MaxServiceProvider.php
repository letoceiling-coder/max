<?php

namespace LetoceilingCoder\Max;

use Illuminate\Support\ServiceProvider;
use LetoceilingCoder\Max\Bot;
use LetoceilingCoder\Max\MiniApp;

class MaxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Регистрируем singleton для основных классов
        $this->app->singleton('max.bot', function ($app) {
            return new Bot(config('max.token'));
        });

        $this->app->singleton('max.miniapp', function ($app) {
            return new MiniApp(config('max.secret_key'));
        });

        // Алиасы
        $this->app->alias('max.bot', Bot::class);
        $this->app->alias('max.miniapp', MiniApp::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Публикация конфига
        $this->publishes([
            __DIR__.'/../config/max.php' => config_path('max.php'),
        ], 'max-config');
    }
}

