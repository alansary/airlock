<?php

namespace Laravel\Airlock\Tests;

use Illuminate\Http\Request;
use Laravel\Airlock\AirlockServiceProvider;
use Laravel\Airlock\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Orchestra\Testbench\TestCase;

class EnsureFrontendRequestsAreStatefulTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('airlock.stateful', ['test.com', '*.test.com']);
    }

    public function test_request_referer_is_parsed_against_configuration()
    {
        $request = Request::create('/');
        $request->headers->set('referer', 'https://test.com');

        $this->assertTrue(EnsureFrontendRequestsAreStateful::fromFrontend($request));

        $request = Request::create('/');
        $request->headers->set('referer', 'https://wrong.com');

        $this->assertFalse(EnsureFrontendRequestsAreStateful::fromFrontend($request));
    }

    public function test_wildcard_matching()
    {
        $request = Request::create('/');
        $request->headers->set('referer', 'https://foo.test.com');

        $this->assertTrue(EnsureFrontendRequestsAreStateful::fromFrontend($request));
    }

    protected function getPackageProviders($app)
    {
        return [AirlockServiceProvider::class];
    }
}
