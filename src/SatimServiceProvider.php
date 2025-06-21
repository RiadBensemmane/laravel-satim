<?php

declare(strict_types=1);

namespace LaravelSatim;

use Illuminate\Support\ServiceProvider;
use LaravelSatim\Contracts\SatimInterface;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 * @project laravel-satim
 * @package LaravelSatim
 * @name SatimServiceProvider
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 * @version 1.0.0
 */
class SatimServiceProvider extends ServiceProvider
{
    /**
     * @return void
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/satim.php' => config_path('satim.php')], 'config');
        }
    }

    /**
     * @return void
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/satim.php', 'satim');
        $this->registerServices();
    }

    /**
     * @return void
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    protected function registerServices(): void
    {
        $this->app->singleton(SatimInterface::class, Satim::class);
    }
}
