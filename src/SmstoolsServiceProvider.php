<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi;

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use Illuminate\Support\ServiceProvider;

class SmstoolsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/smstools.php',
            'smstools'
        );

        $this->app->singleton(SmstoolsConnector::class, function () {
            $clientId     = (string) config('smstools.client_id', '');
            $clientSecret = (string) config('smstools.client_secret', '');

            if (empty($clientId)) {
                throw new \RuntimeException(
                    'Smstools client ID is not configured. Set SMSTOOLS_CLIENT_ID in your .env file.'
                );
            }

            if (empty($clientSecret)) {
                throw new \RuntimeException(
                    'Smstools client secret is not configured. Set SMSTOOLS_CLIENT_SECRET in your .env file.'
                );
            }

            return new SmstoolsConnector(
                clientId: $clientId,
                clientSecret: $clientSecret,
                baseUrl: (string) config('smstools.base_url', 'https://api.smsgatewayapi.com/v1'),
            );
        });

        $this->app->singleton(SmstoolsClient::class, fn ($app) => new SmstoolsClient(
            connector: $app->make(SmstoolsConnector::class),
        ));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/smstools.php' => config_path('smstools.php'),
            ], 'smstools-config');
        }
    }
}
