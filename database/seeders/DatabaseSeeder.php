<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::firstOrCreate(
            ['username' => 'Huri'],
            [
                'name' => 'Huri',
                'email' => 'huri@logit.local',
                'password' => Hash::make('Rh140502'),
                'role' => 'admin',
                'phone_number' => null,
                'is_active' => true,
            ]
        );

        // 2. Kategori Pekerjaan IT Support
        $categories = [
            [
                'name' => 'Hardware & Software (Komputer)',
                'slug' => 'hardware-software-komputer',
                'code' => 'PC',
                'default_sla_minutes' => 60,
                'icon' => 'heroicon-o-computer-desktop',
                'color_hex' => '#3B82F6',
            ],
            [
                'name' => 'Infrastruktur & Daya (Power)',
                'slug' => 'infrastruktur-daya-power',
                'code' => 'PWR',
                'default_sla_minutes' => 45,
                'icon' => 'heroicon-o-bolt',
                'color_hex' => '#F59E0B',
            ],
            [
                'name' => 'CCTV & Keamanan',
                'slug' => 'cctv-keamanan',
                'code' => 'CCTV',
                'default_sla_minutes' => 45,
                'icon' => 'heroicon-o-video-camera',
                'color_hex' => '#EF4444',
            ],
            [
                'name' => 'Perawatan Fisik (General Maintenance)',
                'slug' => 'perawatan-fisik-general-maintenance',
                'code' => 'MTC',
                'default_sla_minutes' => 60,
                'icon' => 'heroicon-o-sparkles',
                'color_hex' => '#10B981',
            ],
            [
                'name' => 'Support Operasional & Administrasi',
                'slug' => 'support-operasional-administrasi',
                'code' => 'OPS',
                'default_sla_minutes' => 30,
                'icon' => 'heroicon-o-document-text',
                'color_hex' => '#8B5CF6',
            ],
            [
                'name' => 'Lain-lain (Others)',
                'slug' => 'lain-lain-others',
                'code' => 'OTH',
                'default_sla_minutes' => 60,
                'icon' => 'heroicon-o-ellipsis-horizontal-circle',
                'color_hex' => '#6B7280',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Department / Divisi
        $departments = [
            ['code' => 'IT',   'name' => 'IT',       'floor_location' => null],
            ['code' => 'FA',   'name' => 'Fa',       'floor_location' => null],
            ['code' => 'EXIM', 'name' => 'Exim',     'floor_location' => null],
            ['code' => 'HRD',  'name' => 'HRD',      'floor_location' => null],
            ['code' => 'RND',  'name' => 'R&D',      'floor_location' => null],
            ['code' => 'LAB',  'name' => 'Lab',      'floor_location' => null],
            ['code' => 'SC',   'name' => 'SC',       'floor_location' => null],
            ['code' => 'ISO',  'name' => 'ISO',      'floor_location' => null],
            ['code' => 'PROD', 'name' => 'Produksi', 'floor_location' => null],
            ['code' => 'TEK',  'name' => 'Teknik',   'floor_location' => null],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
