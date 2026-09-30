<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Pemakai;
use App\Models\Supplier;
use App\Models\ServiceCenter;
use App\Models\Barang;
use App\Models\MtMutasi;
use App\Models\DtMutasi;
use App\Models\MService;
use App\Models\MServiceInternal;
use App\Models\ComputerDevice;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventorySystemTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create(['role' => 'admin']);
    }

    public function test_pemakai_crud_with_comp_name()
    {
        $dept = Department::first();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Pemakais\Index::class)
            ->call('openCreateModal')
            ->set('nama', 'Budi Test')
            ->set('comp_name', 'TEST-PC-01')
            ->set('department_id', $dept?->id)
            ->call('save');

        $pemakai = Pemakai::where('nama', 'Budi Test')->first();
        $this->assertNotNull($pemakai);
        $this->assertEquals('TEST-PC-01', $pemakai->comp_name);

        // Edit
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Pemakais\Index::class)
            ->call('openEditModal', $pemakai->id)
            ->set('comp_name', 'TEST-PC-02')
            ->call('save');

        $this->assertEquals('TEST-PC-02', $pemakai->fresh()->comp_name);
    }

    public function test_supplier_and_service_center_crud()
    {
        // Supplier
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Suppliers\Index::class)
            ->call('openCreateModal')
            ->set('nama_supplier', 'PT Vendor Komputer')
            ->set('no_telp', '021-99887766')
            ->call('save');

        $supplier = Supplier::where('nama_supplier', 'PT Vendor Komputer')->first();
        $this->assertNotNull($supplier);

        // Service Center
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ServiceCenters\Index::class)
            ->call('openCreateModal')
            ->set('nama_service', 'Acer Service Center')
            ->set('no_telp', '021-55443322')
            ->call('save');

        $sc = ServiceCenter::where('nama_service', 'Acer Service Center')->first();
        $this->assertNotNull($sc);
    }

    public function test_barang_crud_and_auto_kode()
    {
        $pemakai = Pemakai::first() ?? Pemakai::create(['nama' => 'Staf Test', 'status' => true]);

        // Create with pemakai in Index modal
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Barangs\Index::class)
            ->call('openCreateModal')
            ->set('nama_barang', 'Monitor LG 24 Inch')
            ->set('kategori', 'monitor')
            ->set('m_pemakai_id', $pemakai->id)
            ->call('save')
            ->assertHasNoErrors();

        $barang = Barang::where('nama_barang', 'Monitor LG 24 Inch')->first();
        $this->assertNotNull($barang);
        $this->assertStringStartsWith('B/', $barang->kode_barang);
        $this->assertEquals($pemakai->id, $barang->m_pemakai_id);

        // Create unassigned via Form component
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Barangs\Form::class)
            ->set('nama_barang', 'Printer Epson L3210')
            ->set('kategori', 'printer')
            ->set('m_pemakai_id', null)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('barangs'));

        $printer = Barang::where('nama_barang', 'Printer Epson L3210')->first();
        $this->assertNotNull($printer);
        $this->assertNull($printer->m_pemakai_id);
    }

    public function test_mutasi_perpindahan_updates_barang_owner()
    {
        $pemakai1 = Pemakai::create(['nama' => 'Pemakai Asal', 'status' => true]);
        $pemakai2 = Pemakai::create(['nama' => 'Pemakai Tujuan', 'status' => true]);

        $barang = Barang::create([
            'kode_barang'  => Barang::generateKode(),
            'nama_barang'  => 'Laptop ThinkPad T480',
            'kategori'     => 'laptop',
            'm_pemakai_id' => $pemakai1->id,
            'status'       => 'aktif',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Mutasis\Index::class)
            ->call('openCreateModal')
            ->set('jenis_mutasi', 'perpindahan')
            ->set('tgl_mutasi', now()->format('Y-m-d'))
            ->set('details', [
                [
                    'm_barang_id'  => $barang->id,
                    'pemakai_lama' => $pemakai1->id,
                    'pemakai_baru' => $pemakai2->id,
                    'harga'        => 0,
                ],
            ])
            ->call('save');

        // Verify barang owner is transferred to pemakai2
        $this->assertEquals($pemakai2->id, $barang->fresh()->m_pemakai_id);

        // Also test creating perpindahan via dedicated Form component
        $pemakai3 = Pemakai::create(['nama' => 'Pemakai Ketiga', 'status' => true]);
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Mutasis\Form::class)
            ->set('jenis_mutasi', 'perpindahan')
            ->set('tgl_mutasi', now()->format('Y-m-d'))
            ->set('details', [
                [
                    'm_barang_id'  => $barang->id,
                    'pemakai_lama' => $pemakai2->id,
                    'pemakai_baru' => $pemakai3->id,
                    'harga'        => 0,
                ],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('mutasis'));

        $this->assertEquals($pemakai3->id, $barang->fresh()->m_pemakai_id);
    }

    public function test_mutasi_excludes_already_selected_barang_in_subsequent_rows()
    {
        $pemakaiA = Pemakai::create(['nama' => 'Putra', 'status' => true]);
        $pemakaiB = Pemakai::create(['nama' => 'Stevi', 'status' => true]);

        $barang1 = Barang::create([
            'kode_barang'  => Barang::generateKode(),
            'nama_barang'  => 'Asus ExpertBook Putra',
            'kategori'     => 'laptop',
            'm_pemakai_id' => $pemakaiA->id,
            'status'       => 'aktif',
        ]);

        $barang2 = Barang::create([
            'kode_barang'  => Barang::generateKode(),
            'nama_barang'  => 'Lenovo V14 Stevi',
            'kategori'     => 'laptop',
            'm_pemakai_id' => $pemakaiB->id,
            'status'       => 'aktif',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Mutasis\Index::class)
            ->call('openCreateModal')
            ->call('addDetail') // now 2 rows
            ->set('details.0.m_barang_id', $barang1->id)
            ->assertViewHas('rowBarangOptions', function ($options) use ($barang1, $barang2) {
                // Row 0 should still contain its own selected barang1
                $row0Ids = array_column($options[0], 'id');
                // Row 1 should NOT contain barang1 (it should only contain barang2)
                $row1Ids = array_column($options[1], 'id');

                return in_array($barang1->id, $row0Ids)
                    && !in_array($barang1->id, $row1Ids)
                    && in_array($barang2->id, $row1Ids);
            });
    }

    public function test_service_eksternal_and_internal()
    {
        $pemakai = Pemakai::first() ?? Pemakai::create(['nama' => 'User Laptop', 'status' => true]);
        $sc = ServiceCenter::first() ?? ServiceCenter::create(['nama_service' => 'Vendor Test', 'status' => true]);

        $barang = Barang::create([
            'kode_barang'  => Barang::generateKode(),
            'nama_barang'  => 'Printer Epson L3110',
            'kategori'     => 'printer',
            'm_pemakai_id' => $pemakai->id,
            'status'       => 'aktif',
        ]);

        // Service Eksternal
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Services\Index::class)
            ->call('openCreateModal')
            ->set('no_sj', 'SJ-001')
            ->set('m_barang_id', $barang->id)
            ->set('m_service_center_id', $sc->id)
            ->set('tgl_service', now()->format('Y-m-d'))
            ->set('kerusakan', 'Head buntu tidak bisa print')
            ->call('save');

        $service = MService::where('no_sj', 'SJ-001')->first();
        $this->assertNotNull($service);
        // Barang status becomes sedang_service
        $this->assertEquals('sedang_service', $barang->fresh()->status);

        // Service Internal
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ServiceInternals\Index::class)
            ->call('openCreateModal')
            ->set('m_pemakai_id', $pemakai->id)
            ->set('m_barang_id', $barang->id)
            ->set('tgl_service', now()->format('Y-m-d'))
            ->set('kerusakan', 'Pembersihan roller internal')
            ->call('save');

        $internal = MServiceInternal::where('m_barang_id', $barang->id)->first();
        $this->assertNotNull($internal);
    }
}
