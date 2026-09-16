<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminRouteContractTest extends TestCase
{
    public function test_admin_product_web_routes_are_registered(): void
    {
        $this->assertNotNull(app('router')->getRoutes()->getByName('admin.products.index'));
        $this->assertNotNull(app('router')->getRoutes()->getByName('admin.products.create'));
        $this->assertNotNull(app('router')->getRoutes()->getByName('admin.products.show'));
    }

    public function test_admin_users_and_report_api_routes_are_registered(): void
    {
        $routes = app('router')->getRoutes();

        $this->assertNotNull($routes->getByName('admin.products.index'));
        $this->assertNotNull($routes->getByAction('App\\Http\\Controllers\\Api\\Admin\\UserController@lock'));
        $this->assertNotNull($routes->getByAction('App\\Http\\Controllers\\Api\\Admin\\ReportController@summary'));
    }
}
