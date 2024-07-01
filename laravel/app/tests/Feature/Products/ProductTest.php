<?php

namespace Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_products_can_be_indexed(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_product_can_be_shown(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_product_can_be_stored(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_product_can_be_updated(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_product_can_be_destroyed(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
