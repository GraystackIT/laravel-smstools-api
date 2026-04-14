<?php

declare(strict_types=1);

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
