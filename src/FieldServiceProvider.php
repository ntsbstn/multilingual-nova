<?php

namespace Digitalcloud\MultilingualNova;

use Digitalcloud\MultilingualNova\Helper\MultilingualHelper;
use Digitalcloud\MultilingualNova\Http\Middleware\Authorize;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FieldServiceProvider extends PackageServiceProvider
{

    /**
     * @param Package $package
     */
    public function configurePackage(Package $package): void
    {
        $package->name('multilingual-nova')
            ->hasConfigFile(['multilingual']);
    }

    public function bootingPackage()
    {

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-language-tool');

        $lang = request('lang', request()->header('lang'));
        $requestUrl = parse_url(request()->header('referer'));
        if(isset($requestUrl['query'])){
            parse_str($requestUrl['query'], $params);
            $lang = $params['lang'] ?? request()->language;
        }
        if ($lang) {
            App::setLocale($lang);
        }
        if($lang = request('lang', request()->header('lang'))){
            App::setLocale($lang);
        }

        Nova::serving(function () {
            Nova::provideToScript(['locals' => MultilingualHelper::getSupportLocales(), 'currentLocal' => App::getLocale()]);
            Nova::script('multilingual-nova', __DIR__ . '/../dist/js/field.js');
            Nova::style('multilingual-nova', __DIR__ . '/../dist/css/field.css');
        });

        $this->app->booted(function () {
            $this->routes();
        });

    }


    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authorize::class], 'multilingual-nova')
            ->group(function ($router) {
                $router->get('multilingual-nova', function ($request) {
                    return inertia('MultilingualNova');
                });
            });

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/multilingual-nova')
            ->group(__DIR__ . '/../routes/api.php');
    }

}
