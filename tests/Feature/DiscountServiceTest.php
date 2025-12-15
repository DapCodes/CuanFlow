<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DiscountService;
    }

    /** @test */
    public function it_calculates_percentage_discount_correctly()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => 1,
                'product_name' => 'Product 1',
                'quantity' => 2,
                'unit_price' => 100000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            200000
        );

        $this->assertEquals(40000, $plan['total_discount']);
        $this->assertEquals('percentage', $plan['discount_type']);
    }

    /** @test */
    public function it_respects_max_discount_cap()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 50,
            'max_discount' => 30000,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => 1,
                'product_name' => 'Product 1',
                'quantity' => 1,
                'unit_price' => 100000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            100000
        );

        // 50% of 100k = 50k, but capped at 30k
        $this->assertEquals(30000, $plan['total_discount']);
    }

    /** @test */
    public function it_applies_fixed_discount()
    {
        $discount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 25000,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => 1,
                'product_name' => 'Product 1',
                'quantity' => 2,
                'unit_price' => 100000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            200000
        );

        $this->assertEquals(25000, $plan['total_discount']);
    }

    /** @test */
    public function it_returns_zero_when_min_purchase_not_met()
    {
        $discount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 50000,
            'min_purchase' => 500000,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => 1,
                'product_name' => 'Product 1',
                'quantity' => 2,
                'unit_price' => 100000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            200000
        );

        $this->assertEquals(0, $plan['total_discount']);
    }

    /** @test */
    public function it_calculates_buy_x_get_y_correctly()
    {
        $product = Product::factory()->create(['selling_price' => 100000]);

        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'buy_quantity' => 3,
            'get_quantity' => 1,
            'product_id' => $product->id,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 4,
                'unit_price' => 100000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            400000
        );

        $this->assertTrue($plan['requires_free_item_selection']);
        $this->assertEquals(1, $plan['free_item_quota']);
        $this->assertEquals(100000, $plan['total_discount']); // Estimated
    }

    /** @test */
    public function it_handles_multiple_free_items_in_buy_x_get_y()
    {
        $product = Product::factory()->create(['selling_price' => 50000]);

        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'buy_quantity' => 2,
            'get_quantity' => 1,
            'product_id' => $product->id,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 6,
                'unit_price' => 50000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            300000
        );

        // floor(6/2) * 1 = 3 free items
        $this->assertEquals(3, $plan['free_item_quota']);
    }

    /** @test */
    public function it_applies_free_items_correctly()
    {
        $product = Product::factory()->create(['selling_price' => 100000]);

        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'buy_quantity' => 3,
            'get_quantity' => 1,
            'product_id' => $product->id,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 4,
                'unit_price' => 100000,
            ],
        ];

        $freeItemSelection = [
            $product->id => 1,
        ];

        $result = $this->service->applyFreeItems(
            $discount,
            $cartItems,
            $freeItemSelection
        );

        $this->assertEquals(100000, $result['total_discount']);
        $this->assertCount(1, $result['affected_items']);
    }

    /** @test */
    public function it_rejects_excessive_free_item_selection()
    {
        $product = Product::factory()->create(['selling_price' => 100000]);

        $discount = Discount::factory()->create([
            'type' => 'buy_x_get_y',
            'buy_quantity' => 3,
            'get_quantity' => 1,
            'product_id' => $product->id,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 4,
                'unit_price' => 100000,
            ],
        ];

        $freeItemSelection = [
            $product->id => 2, // Trying to claim 2 when only 1 allowed
        ];

        $this->expectException(\InvalidArgumentException::class);

        $this->service->applyFreeItems(
            $discount,
            $cartItems,
            $freeItemSelection
        );
    }

    /** @test */
    public function it_selects_best_discount_among_candidates()
    {
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

        $cartItems = [
            [
                'product_id' => 1,
                'product_name' => 'Product 1',
                'quantity' => 2,
                'unit_price' => 100000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount1, $discount2],
            200000
        );

        // Should pick fixed 30k over 10% (20k)
        $this->assertEquals($discount2->id, $plan['discount_id']);
        $this->assertEquals(30000, $plan['total_discount']);
    }

    /** @test */
    public function it_distributes_discount_proportionally_to_items()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => 1,
                'product_name' => 'Product 1',
                'quantity' => 1,
                'unit_price' => 100000,
            ],
            [
                'product_id' => 2,
                'product_name' => 'Product 2',
                'quantity' => 1,
                'unit_price' => 50000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            150000
        );

        // Total discount: 20% of 150k = 30k
        // Item 1: 100k/150k * 30k = 20k
        // Item 2: 50k/150k * 30k = 10k

        $this->assertEquals(30000, $plan['total_discount']);
        $this->assertCount(2, $plan['affected_items']);

        $item1Discount = collect($plan['affected_items'])
            ->firstWhere('product_id', 1)['discount_amount'];
        $this->assertEquals(20000, $item1Discount);

        $item2Discount = collect($plan['affected_items'])
            ->firstWhere('product_id', 2)['discount_amount'];
        $this->assertEquals(10000, $item2Discount);
    }

    /** @test */
    public function it_validates_discount_eligibility()
    {
        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'min_purchase' => 500000,
            'is_active' => true,
        ]);

        $errors = $this->service->validateDiscount($discount, 200000);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Minimum purchase', $errors[0]);
    }

    /** @test */
    public function it_filters_product_level_discount()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'product_id' => $product1->id,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => $product1->id,
                'product_name' => $product1->name,
                'quantity' => 1,
                'unit_price' => 100000,
            ],
            [
                'product_id' => $product2->id,
                'product_name' => $product2->name,
                'quantity' => 1,
                'unit_price' => 50000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            150000
        );

        // Only product1 should get discount
        $this->assertEquals(20000, $plan['total_discount']); // 20% of 100k
        $this->assertCount(1, $plan['affected_items']);
    }

    /** @test */
    public function it_filters_category_level_discount()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        $discount = Discount::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'category_id' => $category1->id,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => $product1->id,
                'product_name' => $product1->name,
                'quantity' => 1,
                'unit_price' => 100000,
            ],
            [
                'product_id' => $product2->id,
                'product_name' => $product2->name,
                'quantity' => 1,
                'unit_price' => 50000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            150000
        );

        // Only product1 (from category1) should get discount
        $this->assertEquals(20000, $plan['total_discount']);
        $this->assertCount(1, $plan['affected_items']);
    }

    /** @test */
    public function it_prevents_negative_discount()
    {
        $discount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 100000,
            'is_active' => true,
        ]);

        $cartItems = [
            [
                'product_id' => 1,
                'product_name' => 'Product 1',
                'quantity' => 1,
                'unit_price' => 50000,
            ],
        ];

        $plan = $this->service->calculateDiscountPlan(
            $cartItems,
            [$discount],
            50000
        );

        // Discount should be capped at subtotal (50k)
        $this->assertEquals(50000, $plan['total_discount']);
    }
}
