<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use DatabaseTransactions;
    public function test_inventory_index_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('inventory.items'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Barang & Aset IT');
        $response->assertSee('Tambah Barang');
    }

    public function test_inventory_create_page_is_accessible(): void
    {
        $user = User::factory()->create();

        $this->withoutExceptionHandling();
        $response = $this->actingAs($user)->get(route('inventory.items.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Barang Baru');
        $response->assertSee('Kode Barang');
    }

    public function test_inventory_edit_page_is_accessible(): void
    {
        $user = User::factory()->create();
        $code = 'TEST-' . rand(1000, 9999);
        $item = InventoryItem::create([
            'kode_barang' => $code,
            'nama_barang' => 'Laptop Testing',
            'kategori' => 'laptop',
            'kondisi' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get(route('inventory.items.edit', $item->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Barang: ' . $code);
        $response->assertSee('Riwayat');
        $response->assertSee('Maintenance');

        // Cleanup
        $item->delete();
    }
}
