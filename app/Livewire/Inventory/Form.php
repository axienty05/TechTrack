<?php

namespace App\Livewire\Inventory;

use App\Models\Department;
use App\Models\InventoryItem;
use App\Models\InventoryHistory;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public ?InventoryItem $item = null;
    public string $activeTab = 'detail';

    // ── Field utama barang ──────────────────────────────────────────
    public string $kode_barang = '';
    public string $nama_barang = '';
    public string $merk_model = '';
    public string $serial_number = '';
    public string $kategori = '';
    public string $lokasi = '';
    public ?int $department_id = null;
    public string $kondisi = 'aktif';
    public string $keterangan = '';
    public string $tgl_perolehan = '';
    public string $harga_perolehan = '';

    // ── Field form history ──────────────────────────────────────────
    public string $history_action = 'perbaikan';
    public string $history_description = '';
    public string $history_date = '';

    // ── Mount ───────────────────────────────────────────────────────
    public function mount($item = null): void
    {
        if ($item instanceof InventoryItem) {
            $this->item = $item;
        } elseif (is_numeric($item)) {
            $this->item = InventoryItem::find($item);
        } else {
            $this->item = null;
        }

        if ($this->item && $this->item->exists) {
            $this->kode_barang = $this->item->kode_barang ?? '';
            $this->nama_barang = $this->item->nama_barang ?? '';
            $this->merk_model = $this->item->merk_model ?? '';
            $this->serial_number = $this->item->serial_number ?? '';
            $this->kategori = $this->item->kategori ?? '';
            $this->lokasi = $this->item->lokasi ?? '';
            $this->department_id = $this->item->department_id;
            $this->kondisi = $this->item->kondisi ?? 'aktif';
            $this->keterangan = $this->item->keterangan ?? '';
            $this->tgl_perolehan = $this->item->tgl_perolehan ? $this->item->tgl_perolehan->format('Y-m-d') : '';
            $this->harga_perolehan = $this->item->harga_perolehan ? (string) $this->item->harga_perolehan : '';
        } else {
            $this->item = null;
            $last = InventoryItem::latest('id')->first();
            $this->kode_barang = 'BRG-' . str_pad(($last ? $last->id + 1 : 1), 4, '0', STR_PAD_LEFT);
        }

        $this->history_date = now()->format('Y-m-d');
    }

    // ── Rules ────────────────────────────────────────────────────────
    protected function rules(): array
    {
        $itemId = $this->item?->id;
        return [
            'kode_barang' => ['required', 'string', 'max:15', Rule::unique('inventory_items', 'kode_barang')->ignore($itemId)],
            'nama_barang' => 'required|string|max:150',
            'merk_model' => 'nullable|string|max:100',
            'serial_number' => ['nullable', 'string', 'max:80', Rule::unique('inventory_items', 'serial_number')->ignore($itemId)],
            'kategori' => 'required|string|max:50',
            'lokasi' => 'nullable|string|max:80',
            'department_id' => 'nullable|exists:departments,id',
            'kondisi' => 'required|string|max:30',
            'keterangan' => 'nullable|string',
            'tgl_perolehan' => 'nullable|date',
            'harga_perolehan' => 'nullable|numeric|min:0',
        ];
    }

    // ── Simpan barang ────────────────────────────────────────────────
    public function save(): void
    {
        $this->validate();

        $data = [
            'kode_barang' => $this->kode_barang,
            'nama_barang' => $this->nama_barang,
            'merk_model' => $this->merk_model ?: null,
            'serial_number' => $this->serial_number ?: null,
            'kategori' => $this->kategori,
            'lokasi' => $this->lokasi ?: null,
            'department_id' => $this->department_id,
            'kondisi' => $this->kondisi,
            'keterangan' => $this->keterangan ?: null,
            'tgl_perolehan' => $this->tgl_perolehan ?: null,
            'harga_perolehan' => $this->harga_perolehan ?: null,
        ];

        if ($this->item && $this->item->exists) {
            $this->item->update($data);
            $msg = 'Data barang berhasil diperbarui.';
        } else {
            $this->item = InventoryItem::create($data);
            $msg = 'Barang baru berhasil ditambahkan.';
        }

        $this->success($msg);
        $this->redirect(route('inventory.items'), navigate: true);
    }

    // ── Simpan riwayat ───────────────────────────────────────────────
    public function saveHistory(): void
    {
        if (!$this->item || !$this->item->exists) {
            return;
        }

        $this->validate([
            'history_action' => 'required|string',
            'history_description' => 'required|string|max:1000',
            'history_date' => 'required|date',
        ]);

        InventoryHistory::create([
            'inventory_item_id' => $this->item->id,
            'user_id' => auth()->id(),
            'action' => $this->history_action,
            'description' => $this->history_description,
            'action_date' => $this->history_date,
        ]);

        $this->success('Riwayat berhasil ditambahkan.');
        $this->reset(['history_description']);
        $this->history_date = now()->format('Y-m-d');
        $this->history_action = 'perbaikan';
    }

    // ── Hapus riwayat ────────────────────────────────────────────────
    public function deleteHistory(int $historyId): void
    {
        InventoryHistory::find($historyId)?->delete();
        $this->success('Riwayat berhasil dihapus.');
    }

    // ── Render ───────────────────────────────────────────────────────
    public function render()
    {
        $departments = Department::orderBy('name')->get();
        $histories = $this->item && $this->item->exists ? $this->item->histories()->with('user')->get() : collect();

        return view('livewire.inventory.form', compact('departments', 'histories'));
    }
}
