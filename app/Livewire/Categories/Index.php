<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Str;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showModal = false;
    public ?int $categoryId = null;

    public string $name = '';
    public string $code = '';
    public string $slug = '';
    public int $default_sla_minutes = 60;
    public string $icon = 'o-tag';
    public string $color_hex = '#3b82f6';

    public function openCreateModal()
    {
        $this->reset(['categoryId', 'name', 'code', 'slug']);
        $this->default_sla_minutes = 60;
        $this->icon = 'o-tag';
        $this->color_hex = '#3b82f6';
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $item = Category::findOrFail($id);
        $this->categoryId = $item->id;
        $this->name = $item->name;
        $this->code = $item->code;
        $this->slug = $item->slug;
        $this->default_sla_minutes = (int) ($item->default_sla_minutes ?? 60);
        $this->icon = $item->icon ?? 'o-tag';
        $this->color_hex = $item->color_hex ?? '#3b82f6';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:categories,code,' . $this->categoryId,
            'default_sla_minutes' => 'required|integer|min:1',
        ]);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'slug' => Str::slug($this->name),
            'default_sla_minutes' => $this->default_sla_minutes,
            'icon' => $this->icon ?: 'o-tag',
            'color_hex' => $this->color_hex ?: '#3b82f6',
        ];

        if ($this->categoryId) {
            Category::findOrFail($this->categoryId)->update($data);
            $this->success('Kategori berhasil diperbarui!');
        } else {
            Category::create($data);
            $this->success('Kategori baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $cat = Category::withCount('workLogs')->findOrFail($id);
        if ($cat->work_logs_count > 0) {
            $this->error("Kategori tidak dapat dihapus karena memiliki {$cat->work_logs_count} tiket pekerjaan.");
            return;
        }

        $cat->delete();
        $this->success('Kategori berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';
        $categories = Category::withCount('workLogs')
            ->when($this->search, function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('code', 'like', $term);
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.categories.index', ['categories' => $categories]);
    }
}