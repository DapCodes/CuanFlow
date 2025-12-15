<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $outlet;

    protected $product1;

    protected $product2;

    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outlet = Outlet::factory()->create();
        $this->user = User::factory()->create(['outlet_id' => $this->outlet->id]);

        $this->category = Category::factory()->create([
            'type' => 'product',
            'is_active' => true,
        ]);

        $unit = Unit::factory()->create();

        $this->product1 = Product::factory()->create([
            'outlet_id' => $this->outlet->id,
            'category_id' => $this->category->id,
            'unit_id' => $unit->id,
            'selling_price' => 100000,
            'hpp' => 50000,
            'track_stock' => true,
        ]);

        $this->product2 = Product::factory()->create([
            'outlet_id' => $this->outlet->id,
            'category_id' => $this->category->id,
            'unit_id' => $unit->id,
            'selling_price' => 50000,
            'hpp' => 25000,
            'track_stock' => true,
        ]);

        ProductStock::create([
            'product_id' => $this->product1->id,
            'outlet_id' => $this->outlet->id,
            'quantity' => 100,
        ]);

        ProductStock::create([
            'product_id' => $this->product2->id,
            'outlet_id' => $this->outlet->id,
            'quantity' => 100,
        ]);

        CashRegister::create([
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->user->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);
    }

    /** @test */
    public function it_applies_percentage_discount_correctly()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'min_purchase' => 0,
            'max_discount' => null,
            'product_id' => null,
            'category_id' => null,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        // Add items to cart
        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'discount_plan' => [
                    'discount_type' => 'percentage',
                    'total_discount' => 40000, // 20% of 200,000
                ],
            ]);
    }

    /** @test */
    public function it_applies_max_discount_cap_on_percentage()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'min_purchase' => 0,
            'max_discount' => 30000, // Cap at 30k
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('discount_plan.total_discount', 30000);
    }

    /** @test */
    public function it_rejects_discount_when_min_purchase_not_met()
    {
        $discount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 20000,
            'min_purchase' => 500000, // Minimum 500k
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function it_applies_fixed_discount_correctly()
    {
        $discount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 20000,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('discount_plan.total_discount', 20000);
    }

    /** @test */
    public function it_handles_buy_x_get_y_discount()
    {
        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'value' => 0,
            'buy_quantity' => 3,
            'get_quantity' => 1,
            'product_id' => $this->product1->id,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 4,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'discount_plan' => [
                    'discount_type' => 'buy_x_get_y',
                    'requires_free_item_selection' => true,
                    'free_item_quota' => 1,
                ],
            ]);
    }

    /** @test */
    public function it_assigns_free_items_for_buy_x_get_y()
    {
        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'buy_quantity' => 3,
            'get_quantity' => 1,
            'product_id' => $this->product1->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session([
            'pos_cart' => [
                $this->product1->id => [
                    'product_id' => $this->product1->id,
                    'product_name' => $this->product1->name,
                    'product_code' => $this->product1->code,
                    'quantity' => 4,
                    'unit_price' => 100000,
                    'hpp' => 50000,
                ],
            ],
            'pos_discount_plan' => [
                'discount_id' => $discount->id,
                'discount_type' => 'buy_x_get_y',
                'requires_free_item_selection' => true,
                'free_item_quota' => 1,
            ],
        ]);

        $response = $this->postJson(route('pos.discounts.assign-free-items'), [
            'free_items' => [
                ['product_id' => $this->product1->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('discount_plan.total_discount', 100000);
    }

    /** @test */
    public function it_handles_multiple_freebies_in_buy_x_get_y()
    {
        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'buy_quantity' => 3,
            'get_quantity' => 2,
            'product_id' => $this->product1->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session([
            'pos_cart' => [
                $this->product1->id => [
                    'product_id' => $this->product1->id,
                    'product_name' => $this->product1->name,
                    'product_code' => $this->product1->code,
                    'quantity' => 6,
                    'unit_price' => 100000,
                    'hpp' => 50000,
                ],
            ],
            'pos_discount_plan' => [
                'discount_id' => $discount->id,
                'discount_type' => 'buy_x_get_y',
                'requires_free_item_selection' => true,
                'free_item_quota' => 4, // floor(6/3) * 2 = 4
            ],
        ]);

        $response = $this->postJson(route('pos.discounts.assign-free-items'), [
            'free_items' => [
                ['product_id' => $this->product1->id, 'quantity' => 4],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('discount_plan.total_discount', 400000);
    }

    /** @test */
    public function it_chooses_best_discount_when_multiple_available()
    {
        // Create two discounts
        $discount1 = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        $discount2 = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 30000,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        // Apply without code - should pick fixed 30k (better than 10% of 200k = 20k)
        $response = $this->postJson(route('pos.discounts.apply'));

        $response->assertStatus(200)
            ->assertJsonPath('discount_plan.total_discount', 30000)
            ->assertJsonPath('discount_plan.discount_id', $discount2->id);
    }

    /** @test */
    public function discount_code_overrides_auto_discount()
    {
        $autoDiscount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 30000,
            'is_active' => true,
        ]);

        $codeDiscount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 15,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $codeDiscount->code,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('discount_plan.discount_id', $codeDiscount->id);
    }

    /** @test */
    public function it_reduces_stock_correctly_with_discount()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        $initialStock = $this->product1->getStockQuantity($this->outlet->id);

        session([
            'pos_cart' => [
                $this->product1->id => [
                    'product_id' => $this->product1->id,
                    'product_name' => $this->product1->name,
                    'product_code' => $this->product1->code,
                    'quantity' => 2,
                    'unit_price' => 100000,
                    'hpp' => 50000,
                ],
            ],
            'pos_discount_plan' => [
                'discount_id' => $discount->id,
                'discount_type' => 'percentage',
                'total_discount' => 40000,
                'affected_items' => [],
            ],
        ]);

        $response = $this->postJson(route('payment.cash'), [
            'paid_amount' => 200000,
        ]);

        $response->assertStatus(200);

        $this->assertEquals(
            $initialStock - 2,
            $this->product1->fresh()->getStockQuantity($this->outlet->id)
        );
    }

    /** @test */
    public function it_increments_discount_usage_on_successful_payment()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'usage_limit' => 10,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session([
            'pos_cart' => [
                $this->product1->id => [
                    'product_id' => $this->product1->id,
                    'product_name' => $this->product1->name,
                    'product_code' => $this->product1->code,
                    'quantity' => 2,
                    'unit_price' => 100000,
                    'hpp' => 50000,
                ],
            ],
            'pos_discount_plan' => [
                'discount_id' => $discount->id,
                'discount_type' => 'percentage',
                'total_discount' => 40000,
                'affected_items' => [],
            ],
        ]);

        $this->postJson(route('payment.cash'), [
            'paid_amount' => 200000,
        ]);

        $this->assertEquals(1, $discount->fresh()->used_count);
    }

    /** @test */
    public function it_calculates_tax_after_discount()
    {
        $discount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 50000,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session([
            'pos_cart' => [
                $this->product1->id => [
                    'product_id' => $this->product1->id,
                    'product_name' => $this->product1->name,
                    'product_code' => $this->product1->code,
                    'quantity' => 2,
                    'unit_price' => 100000,
                    'hpp' => 50000,
                ],
            ],
        ]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $summary = $response->json('cart_summary');

        // Subtotal: 200,000
        // Discount: 50,000
        // Subtotal after discount: 150,000
        // Tax (0%): 0
        // Grand total: 150,000

        $this->assertEquals(200000, $summary['subtotal']);
        $this->assertEquals(50000, $summary['total_discount']);
        $this->assertEquals(150000, $summary['grand_total']);
    }

    /** @test */
    public function it_validates_free_item_quantity_limit()
    {
        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'buy_quantity' => 3,
            'get_quantity' => 1,
            'product_id' => $this->product1->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session([
            'pos_cart' => [
                $this->product1->id => [
                    'product_id' => $this->product1->id,
                    'product_name' => $this->product1->name,
                    'product_code' => $this->product1->code,
                    'quantity' => 4,
                    'unit_price' => 100000,
                    'hpp' => 50000,
                ],
            ],
            'pos_discount_plan' => [
                'discount_id' => $discount->id,
                'discount_type' => 'buy_x_get_y',
                'free_item_quota' => 1,
            ],
        ]);

        // Try to claim 2 free items when only 1 is allowed
        $response = $this->postJson(route('pos.discounts.assign-free-items'), [
            'free_items' => [
                ['product_id' => $this->product1->id, 'quantity' => 2],
            ],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function it_rejects_expired_discount()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'end_date' => now()->subDay(),
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_rejects_discount_over_usage_limit()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'usage_limit' => 5,
            'used_count' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        session(['pos_cart' => [
            $this->product1->id => [
                'product_id' => $this->product1->id,
                'product_name' => $this->product1->name,
                'product_code' => $this->product1->code,
                'quantity' => 2,
                'unit_price' => 100000,
                'hpp' => 50000,
            ],
        ]]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(404);
    }
}
