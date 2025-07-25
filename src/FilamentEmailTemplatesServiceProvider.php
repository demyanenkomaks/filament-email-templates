<?php

namespace Maksde\FilamentEmailTemplates;

use Maksde\FilamentEmailTemplates\Contracts\CreateMailableInterface;
use Maksde\FilamentEmailTemplates\Helpers\CreateMailableHelper;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentEmailTemplatesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-email-templates')
            ->hasConfigFile(['filament-email-templates'])
            ->hasMigrations(['create_email_templates_table'])
            ->hasTranslations()
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('demyanenkomaks/filament-email-templates');
            });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'md-filament-email-templates');
        $this->loadViewsFrom(__DIR__.'/../resources/dist', 'md-filament-email-templates');
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton(CreateMailableInterface::class, CreateMailableHelper::class);
    }
}
