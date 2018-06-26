<?php

namespace Arjasco\LaravelRecaptcha;

use Arjasco\LaravelRecaptcha\RecaptchaValidator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use GuzzleHttp\Client;

class RecaptchaServiceProvider extends ServiceProvider
{
    /**
     * Boot the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/recaptcha-config.php' => config_path('recaptcha.php'),
        ]);

        if (file_exists($helpers = __DIR__.'/helpers.php')) {
            require $helpers;
        }

        $this->app['validator']->extendImplicit(
            'recaptcha', RecaptchaValidator::class.'@validate'
        );

        Blade::directive('recaptcha', function ($options) {
            return "<?php echo recaptcha($options); ?>";
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('recaptcha', function () {
            return new Recaptcha(
                new Client,
                $this->app['config']['recaptcha'] ?? []
            );
        });

        $this->app->alias('recaptcha', Recaptcha::class);
    }
}
