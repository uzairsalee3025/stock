<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'a@test.com',
            'password' => 'password', 'role' => User::ROLE_ADMIN, 'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Sign In');
    }

    public function test_all_main_pages_render_for_admin(): void
    {
        $cat = IngredientCategory::create(['name' => 'Herbs']);
        Ingredient::create(['ingredient_category_id' => $cat->id, 'name' => 'Rice', 'unit' => 'kg', 'available_quantity' => 0]);
        $pcat = ProductCategory::create(['name' => 'Bottles']);
        Product::create(['product_category_id' => $pcat->id, 'name' => 'Oil', 'quantity_stock' => 10]);

        $pages = [
            '/', '/patients', '/patients/create',
            '/ingredients', '/ingredients/create', '/ingredient-categories',
            '/suppliers', '/suppliers/create',
            '/ingredient-usages', '/ingredient-usages/create',
            '/products', '/products/create', '/product-categories',
            '/product-sales', '/product-sales/create',
            '/reports/patients', '/reports/ingredients', '/reports/products',
        ];

        $admin = $this->admin();
        foreach ($pages as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_patient_serial_is_generated_and_slip_uploads(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())->post('/patients', [
            'name' => 'John Doe',
            'phone' => '12345',
            'age' => 30,
            'gender' => 'male',
            'visit_date' => now()->toDateString(),
            'doctor_name' => 'Dr. Smith',
            'status' => 'active',
            'prescription' => UploadedFile::fake()->create('slip.pdf', 100, 'application/pdf'),
        ]);

        $patient = \App\Models\Patient::first();
        $this->assertNotNull($patient);
        $this->assertStringStartsWith('PT-'.now()->format('Y').'-', $patient->serial_number);
        $this->assertCount(1, $patient->visits);
        Storage::disk('public')->assertExists($patient->visits->first()->prescription_path);
        $response->assertRedirect(route('patients.show', $patient));
    }

    public function test_same_ingredient_from_different_suppliers_sums_into_total(): void
    {
        $cat = IngredientCategory::create(['name' => 'Grains']);
        $admin = $this->admin();

        // The doc's Rice example: 3 supplier entries via the New Ingredient form.
        $suppliers = [['Ahmed', 30, 600], ['Uzair', 40, 500], ['Ammar', 30, 550]];
        foreach ($suppliers as [$name, $qty, $price]) {
            $this->actingAs($admin)->post('/ingredients', [
                'ingredient_category_id' => $cat->id,
                'name' => 'Rice', 'unit' => 'kg',
                'supplier_name' => $name, 'quantity' => $qty, 'price' => $price,
                'date' => now()->toDateString(),
            ])->assertRedirect();
        }

        // Should merge into ONE Rice ingredient with 3 entries totalling 100kg.
        $this->assertEquals(1, Ingredient::where('name', 'Rice')->count());
        $rice = Ingredient::where('name', 'Rice')->first();
        $this->assertEquals(100, (float) $rice->available_quantity);
        $this->assertCount(3, $rice->stockEntries);
    }

    public function test_usage_deducts_and_validates_available_quantity(): void
    {
        $cat = IngredientCategory::create(['name' => 'Grains']);
        $rice = Ingredient::create(['ingredient_category_id' => $cat->id, 'name' => 'Rice', 'unit' => 'kg', 'available_quantity' => 100]);
        $admin = $this->admin();

        // Use 30kg -> 70kg remaining.
        $this->actingAs($admin)->post('/ingredient-usages', [
            'ingredient_id' => $rice->id, 'quantity' => 30, 'usage_date' => now()->toDateString(),
        ])->assertRedirect(route('ingredient-usages.index'));
        $this->assertEquals(70, (float) $rice->fresh()->available_quantity);

        // Cannot use more than available.
        $this->actingAs($admin)->post('/ingredient-usages', [
            'ingredient_id' => $rice->id, 'quantity' => 1000, 'usage_date' => now()->toDateString(),
        ])->assertSessionHasErrors('quantity');
        $this->assertEquals(70, (float) $rice->fresh()->available_quantity);

        // Quantity must be > 0.
        $this->actingAs($admin)->post('/ingredient-usages', [
            'ingredient_id' => $rice->id, 'quantity' => 0, 'usage_date' => now()->toDateString(),
        ])->assertSessionHasErrors('quantity');
    }

    public function test_removing_a_supplier_entry_adjusts_total(): void
    {
        $cat = IngredientCategory::create(['name' => 'Grains']);
        $admin = $this->admin();

        $this->actingAs($admin)->post('/ingredients', [
            'ingredient_category_id' => $cat->id, 'name' => 'Sugar', 'unit' => 'kg',
            'supplier_name' => 'Ahmed', 'quantity' => 50, 'price' => 100, 'date' => now()->toDateString(),
        ]);
        $sugar = Ingredient::where('name', 'Sugar')->first();
        $entry = $sugar->stockEntries()->first();
        $this->assertEquals(50, (float) $sugar->available_quantity);

        $this->actingAs($admin)->delete("/entries/{$entry->id}")->assertRedirect();
        $this->assertEquals(0, (float) $sugar->fresh()->available_quantity);
    }

    public function test_product_add_only_requires_three_fields(): void
    {
        $pcat = ProductCategory::create(['name' => 'Oil']);
        $admin = $this->admin();

        // Add product with just category, name, quantity (the doc's example).
        $this->actingAs($admin)->post('/products', [
            'product_category_id' => $pcat->id,
            'name' => 'Herbal Oil Bottle',
            'quantity_stock' => 400,
        ])->assertRedirect();

        $product = Product::where('name', 'Herbal Oil Bottle')->first();
        $this->assertEquals(400, $product->quantity_stock);

        // Missing required fields fail validation.
        $this->actingAs($admin)->post('/products', ['name' => 'X'])
            ->assertSessionHasErrors(['product_category_id', 'quantity_stock']);
    }

    public function test_product_sale_deducts_and_validates_stock(): void
    {
        $pcat = ProductCategory::create(['name' => 'Oil']);
        $product = Product::create(['product_category_id' => $pcat->id, 'name' => 'Herbal Oil Bottle', 'quantity_stock' => 400]);
        $admin = $this->admin();

        // Sell 200 -> 200 remaining.
        $this->actingAs($admin)->post('/product-sales', [
            'product_id' => $product->id, 'sale_quantity' => 200,
        ])->assertRedirect(route('product-sales.index'));
        $this->assertEquals(200, $product->fresh()->quantity_stock);
        $this->assertCount(1, $product->sales);

        // Cannot sell more than available.
        $this->actingAs($admin)->post('/product-sales', [
            'product_id' => $product->id, 'sale_quantity' => 9999,
        ])->assertSessionHasErrors('sale_quantity');
        $this->assertEquals(200, $product->fresh()->quantity_stock);

        // Sale quantity must be > 0.
        $this->actingAs($admin)->post('/product-sales', [
            'product_id' => $product->id, 'sale_quantity' => 0,
        ])->assertSessionHasErrors('sale_quantity');
    }

    public function test_admin_dashboard_shows_all_module_summaries(): void
    {
        $this->actingAs($this->admin())->get('/')
            ->assertOk()
            ->assertSee('Patient Summary')
            ->assertSee('Ingredient Inventory Summary')
            ->assertSee('Product Summary');
    }

    public function test_dashboard_sections_are_scoped_for_non_admins(): void
    {
        $staff = User::create(['name' => 'Staff', 'email' => 'st@test.com', 'password' => 'password', 'role' => User::ROLE_STAFF, 'is_active' => true]);
        $inv = User::create(['name' => 'Inv', 'email' => 'in@test.com', 'password' => 'password', 'role' => User::ROLE_INVENTORY, 'is_active' => true]);

        $this->actingAs($staff)->get('/')
            ->assertSee('Patient Summary')
            ->assertDontSee('Ingredient Inventory Summary');

        $this->actingAs($inv)->get('/')
            ->assertDontSee('Patient Summary')
            ->assertSee('Ingredient Inventory Summary');
    }

    public function test_supplier_breakdown_totals_sum_quantity_not_count(): void
    {
        $cat = IngredientCategory::create(['name' => 'Oils']);
        $admin = $this->admin();

        // Saad 200, Uzair 100 -> totals must be 300, not the 2-row count.
        foreach ([['Saad', 200], ['Uzair', 100]] as [$name, $qty]) {
            $this->actingAs($admin)->post('/ingredients', [
                'ingredient_category_id' => $cat->id, 'name' => 'Coconut Oil', 'unit' => 'kg',
                'supplier_name' => $name, 'quantity' => $qty, 'price' => 0, 'date' => now()->toDateString(),
            ]);
        }
        $oil = Ingredient::where('name', 'Coconut Oil')->first();
        $this->actingAs($admin)->post('/ingredient-usages', [
            'ingredient_id' => $oil->id, 'quantity' => 50, 'usage_date' => now()->toDateString(),
        ]);

        $this->actingAs($admin)->get(route('ingredients.show', $oil))
            ->assertOk()
            ->assertSee('300 kg')    // supplier-wise total quantity (was wrongly "3 kg")
            ->assertSee('250')       // available = 300 - 50 (unit is in a styled span)
            ->assertDontSee('3 kg')  // the old count bug must be gone
            ->assertDontSee('2 kg');

        // Confirm the underlying totals are correct, not just the rendered text.
        $this->assertEquals(300, (float) $oil->stockEntries()->sum('quantity'));
        $this->assertEquals(50, (float) $oil->usages()->sum('quantity'));
        $this->assertEquals(250, (float) $oil->fresh()->available_quantity);
    }

    public function test_user_can_change_own_password(): void
    {
        $user = User::create([
            'name' => 'Pw', 'email' => 'pw@test.com',
            'password' => 'password', 'role' => User::ROLE_STAFF, 'is_active' => true,
        ]);

        // Wrong current password fails.
        $this->actingAs($user)->post('/profile/password', [
            'current_password' => 'wrong', 'password' => 'newsecret8', 'password_confirmation' => 'newsecret8',
        ])->assertSessionHasErrors('current_password');

        // Too-short new password fails.
        $this->actingAs($user)->post('/profile/password', [
            'current_password' => 'password', 'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        // Mismatched confirmation fails.
        $this->actingAs($user)->post('/profile/password', [
            'current_password' => 'password', 'password' => 'newsecret8', 'password_confirmation' => 'different8',
        ])->assertSessionHasErrors('password');

        // Correct change succeeds and the new password actually works (no double-hash).
        $this->actingAs($user)->post('/profile/password', [
            'current_password' => 'password', 'password' => 'newsecret8', 'password_confirmation' => 'newsecret8',
        ])->assertRedirect(route('profile.edit'))->assertSessionHas('success');

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newsecret8', $user->fresh()->password));
    }

    public function test_staff_cannot_access_inventory(): void
    {
        $staff = User::create([
            'name' => 'Staff', 'email' => 's@test.com',
            'password' => 'password', 'role' => User::ROLE_STAFF, 'is_active' => true,
        ]);

        $this->actingAs($staff)->get('/patients')->assertOk();
        $this->actingAs($staff)->get('/ingredients')->assertForbidden();
        $this->actingAs($staff)->get('/ingredient-usages')->assertForbidden();
    }
}
