<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use GraystackIT\SmstoolsApi\SmstoolsServiceProvider;
use Orchestra\Testbench\TestCase;

uses(TestCase::class)->in('Feature');

/**
 * @param  \Orchestra\Testbench\TestCase  $app
 * @return array<int, class-string>
 */
function getPackageProviders($app): array
{
    return [SmstoolsServiceProvider::class];
}

/**
 * Creates a SmstoolsConnector using credentials from environment variables.
 *
 * Marks the test as skipped when SMSTOOLS_CLIENT_ID or SMSTOOLS_CLIENT_SECRET
 * are not set, preventing any attempt to reach the real API with invalid credentials.
 *
 * @throws \PHPUnit\Framework\SkippedWithMessageException when credentials are not configured
 */
function makeConnector(): SmstoolsConnector
{
    $clientId = config('smstools.client_id', '');
    $clientSecret = config('smstools.client_secret', '');

    if (empty($clientId) || empty($clientSecret)) {
        test()->markTestSkipped(
            'Set SMSTOOLS_CLIENT_ID and SMSTOOLS_CLIENT_SECRET in your environment file to run this test.'
        );
    }

    return new SmstoolsConnector(
        clientId: $clientId,
        clientSecret: $clientSecret,
    );
}
