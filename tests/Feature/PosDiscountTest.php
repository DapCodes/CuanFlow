<?php

namespace Tests\Feature;

use App\Models\Discount;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class PosDiscountTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $outlet;

    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outlet = Outlet::factory()->create();
        $this->user = User::factory()->create(['outlet_id' => $this->outlet->id]);
        
        // Setup permission
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Karyawan', 'guard_name' => 'web']);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'akses pos', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $this->user->assignRole($role);

        $this->actingAs($this->user);

        $this->product = Product::factory()->create([
            'outlet_id' => $this->outlet->id,
            'selling_price' => 10000,
            'hpp' => 5000,
            'is_active' => true,
            'track_stock' => false,
        ]);
    }

    public function test_can_apply_percentage_discount()
    {
        $discount = Discount::factory()->percentage()->create([
            'value' => 10, // 10%
            'min_purchase' => 0,
            'is_voucher' => true,
        ]);

        // Add to cart
        $response = $this->postJson(route('pos.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
        
        if ($response->status() !== 200) {
            dump('AddToCart Failed:', $response->json());
        }
        $response->assertStatus(200);

        // Apply discount
        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_plan.total_discount', 2000); // 10% of 20,000
    }

    public function test_can_apply_fixed_discount()
    {
        $discount = Discount::factory()->fixed()->create([
            'value' => 5000,
            'min_purchase' => 0,
            'is_voucher' => true,
        ]);

        // Add to cart
        $this->postJson(route('pos.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Apply discount
        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_plan.total_discount', 5000);
    }

    public function test_can_apply_buy_x_get_y_discount()
    {
        $discount = Discount::factory()->buyXGetY()->create([
            'buy_quantity' => 2,
            'get_quantity' => 1,
            'product_id' => $this->product->id,
            'is_voucher' => true,
        ]);

        // Add 2 items to cart (eligible for 1 free)
        $this->postJson(route('pos.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Apply discount
        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_plan.requires_free_item_selection', true)
            ->assertJsonPath('discount_plan.free_item_quota', 1);

        // Select free item
        $response = $this->postJson(route('pos.discounts.assign-free-items'), [
            'free_items' => [
                ['product_id' => $this->product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_plan.total_discount', 10000); // 1 free item worth 10,000
    }

    public function test_discount_persists_on_cart_update()
    {
        $discount = Discount::factory()->percentage()->create([
            'value' => 10,
            'is_voucher' => true // Ensure it's a voucher as per new rules
        ]);

        $this->postJson(route('pos.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->postJson(route('pos.discounts.apply'), [
            'discount_code' => $discount->code,
        ]);
        
        $response->assertStatus(200);

        $this->assertTrue(Session::has('pos_discount_plan'));

        // Update cart (Add another item)
        $this->postJson(route('pos.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Expect discount to persist and update
        $this->assertTrue(Session::has('pos_discount_plan'));
        
        $plan = Session::get('pos_discount_plan');
        $this->assertEquals(3000, $plan['total_discount']); // 10% of 30,000
    }
}
