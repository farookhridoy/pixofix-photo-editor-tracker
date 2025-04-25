<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_order_index_shows_orders()
    {
        Order::factory()->count(10)->create();

        $response = $this->get(route('orders.index'));
        $response->assertStatus(200);
        $response->assertViewHas('orders');
    }
}
