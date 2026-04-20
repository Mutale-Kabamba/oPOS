<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_a_supplier(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $createResponse = $this->actingAs($admin)->post(route('admin.suppliers.store'), [
            'name' => 'Alpha Vendor',
            'contact_person' => 'Jane Doe',
            'email' => 'alpha@example.com',
            'phone' => '+260970000001',
            'address' => 'Lusaka',
            'is_active' => '1',
        ]);

        $createResponse->assertRedirect(route('admin.suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Alpha Vendor',
            'email' => 'alpha@example.com',
        ]);

        $supplier = Supplier::firstOrFail();

        $updateResponse = $this->actingAs($admin)->put(route('admin.suppliers.update', $supplier), [
            'name' => 'Alpha Vendor Updated',
            'contact_person' => 'John Doe',
            'email' => 'updated@example.com',
            'phone' => '+260970000002',
            'address' => 'Ndola',
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Alpha Vendor Updated',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_supplier_index_supports_search_status_filter_and_pagination(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        Supplier::factory()->create(['name' => 'Target Supplier', 'is_active' => true]);
        Supplier::factory()->create(['name' => 'Inactive Supplier', 'is_active' => false]);
        Supplier::factory()->count(12)->create();

        $response = $this->actingAs($admin)->get(route('admin.suppliers.index', [
            'q' => 'Target',
            'status' => 'active',
        ]));

        $response->assertOk();
        $response->assertSee('Target Supplier');
        $response->assertDontSee('Inactive Supplier');

        $allResponse = $this->actingAs($admin)->get(route('admin.suppliers.index'));
        $allResponse->assertOk();
        $allResponse->assertSee('Next');
    }
}