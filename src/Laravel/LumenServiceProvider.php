<?php

namespace PhantomPdf\Laravel;

use PhantomPdf\PdfGenerator;
use Illuminate\Support\ServiceProvider;

class LumenServiceProvider extends ServiceProvider

{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('phantom-pdf');
        $configPath = __DIR__.'/../config/config.php';
        $this->mergeConfigFrom($configPath, 'phantom-pdf');
    }


    public function boot()
    {
        $configPath = __DIR__.'/../config/config.php';
        $this->mergeConfigFrom($configPath, 'phantom-pdf');

        $this->app->singleton('phantom-pdf', function () {
            $generator = new PdfGenerator;
            $generator->setBaseUrl($this->app['config']['phantom-pdf.base_url'] ?: url('/'));
            $generator->setBinaryPath($this->app['config']['phantom-pdf.binary_path']);
            $generator->setStoragePath($this->app['config']['phantom-pdf.temporary_file_path']);
            $generator->setTimeout($this->app['config']['phantom-pdf.timeout']);
            foreach ($this->app['config']['phantom-pdf.command_line_options'] as $option) {
                $generator->addCommandLineOption($option);
            }

            return $generator;
        });

        $this->app->alias('phantom-pdf', PdfGenerator::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['phantom-pdf', PdfGenerator::class];
    }
}
