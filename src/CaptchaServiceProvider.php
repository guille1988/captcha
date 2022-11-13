<?php


namespace Felipetti\Captcha;


use Felipetti\Captcha\Data\Data;
use Illuminate\Support\ServiceProvider;


class CaptchaServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
       //
    }


    /**
     * Bootstrap any application services.
     *
     * @param Data $data
     * @return void
     */
    public function boot(Data $data): void
    {
        $nameOfConfigTag = str_replace('.php', '', $data->getConfigFileName());
        $configPaths = [$data->getSourcePath() => $data->getPublishPath()];
        $this->publishes($configPaths, $nameOfConfigTag);
    }
}

