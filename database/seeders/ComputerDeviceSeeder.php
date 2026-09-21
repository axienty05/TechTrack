<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\ComputerDevice;

class ComputerDeviceSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan departemen-departemen kunci tersedia
        $departmentsMap = [
            'IT'   => Department::firstOrCreate(['code' => 'IT'], ['name' => 'IT', 'floor_location' => 'Lantai 2']),
            'FA'   => Department::firstOrCreate(['code' => 'FA'], ['name' => 'Finance & Accounting', 'floor_location' => 'Lantai 1']),
            'EXIM' => Department::firstOrCreate(['code' => 'EXIM'], ['name' => 'Exim', 'floor_location' => 'Lantai 1']),
            'HRD'  => Department::firstOrCreate(['code' => 'HRD'], ['name' => 'HRD & GA', 'floor_location' => 'Lantai 1']),
            'RND'  => Department::firstOrCreate(['code' => 'RND'], ['name' => 'R&D', 'floor_location' => 'Lantai 2']),
            'LAB'  => Department::firstOrCreate(['code' => 'LAB'], ['name' => 'Laboratory', 'floor_location' => 'Lantai 2']),
            'PPIC' => Department::firstOrCreate(['code' => 'PPIC'], ['name' => 'PPIC', 'floor_location' => 'Pabrik']),
            'PROD' => Department::firstOrCreate(['code' => 'PROD'], ['name' => 'Produksi', 'floor_location' => 'Pabrik']),
            'TEK'  => Department::firstOrCreate(['code' => 'TEK'], ['name' => 'Teknik', 'floor_location' => 'Pabrik']),
        ];

        // Data Unit Kantor (PT. Aneka Coffee Industry)
        $kantorDevices = [
            ['user' => 'Stephen', 'comp' => 'DESKTOP-RU1L62Q', 'dept' => 'IT', 'type' => 'PC Desktop'],
            ['user' => 'Cisca', 'comp' => 'LOBY2', 'dept' => 'IT', 'type' => 'PC Desktop'],
            ['user' => 'Huri', 'comp' => 'DESKTOP-9I87U2L', 'dept' => 'IT', 'type' => 'PC Desktop'],
            ['user' => 'Inge', 'comp' => 'ACCT-01', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Elvi', 'comp' => 'ACCT-02', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Amelia', 'comp' => 'ACCT-03', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Panggieh', 'comp' => 'ACCT-04', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Alfin', 'comp' => 'ACCT-05', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Elsa', 'comp' => 'ACCT-06', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Lusi', 'comp' => 'FIN-01', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Tatik', 'comp' => 'FIN-02', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Yani', 'comp' => 'FIN-03', 'dept' => 'FA', 'type' => 'PC Desktop'],
            ['user' => 'Pipit', 'comp' => 'EXIM-03', 'dept' => 'EXIM', 'type' => 'PC Desktop'],
            ['user' => 'Yuli', 'comp' => 'EXIM-02', 'dept' => 'EXIM', 'type' => 'PC Desktop'],
            ['user' => 'Susi', 'comp' => 'HRD-01', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Anita', 'comp' => 'HRD-02', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Raning', 'comp' => 'HRD-03', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Sonip', 'comp' => 'HRD-04', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Andi', 'comp' => 'HRD-05', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Indah', 'comp' => 'HRD-06', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Dina', 'comp' => 'HRD-07', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Kuwinarti', 'comp' => 'SECR-01', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Sugandhi', 'comp' => 'SECR-02', 'dept' => 'HRD', 'type' => 'PC Desktop'],
            ['user' => 'Terbit', 'comp' => 'RND-01 Dell', 'dept' => 'RND', 'type' => 'PC Desktop'],
            ['user' => 'Zainal', 'comp' => 'RND-02', 'dept' => 'RND', 'type' => 'PC Desktop'],
            ['user' => 'Ifa', 'comp' => 'RND-03', 'dept' => 'RND', 'type' => 'PC Desktop'],
            ['user' => 'Faris', 'comp' => 'RND-04', 'dept' => 'RND', 'type' => 'PC Desktop'],
            ['user' => 'Yohana', 'comp' => 'RND-05', 'dept' => 'RND', 'type' => 'PC Desktop'],
            ['user' => 'Lily', 'comp' => 'LAB-01', 'dept' => 'LAB', 'type' => 'PC Desktop'],
            ['user' => 'Evi', 'comp' => 'LAB-02', 'dept' => 'LAB', 'type' => 'PC Desktop'],
            ['user' => 'Lab / Ratih', 'comp' => 'LAB-03', 'dept' => 'LAB', 'type' => 'PC Desktop'],
            ['user' => 'User / Vania', 'comp' => 'LAB-04 Dell', 'dept' => 'LAB', 'type' => 'PC Desktop'],
            ['user' => 'Lab / Rosa', 'comp' => 'LAB-06', 'dept' => 'LAB', 'type' => 'PC Desktop'],
            ['user' => 'Lab / Septa', 'comp' => 'LAB-09 Dell', 'dept' => 'LAB', 'type' => 'PC Desktop'],
            ['user' => 'Lab / Esti', 'comp' => 'LAB-10', 'dept' => 'LAB', 'type' => 'PC Desktop'],
            ['user' => 'Lab / Ratno', 'comp' => 'LAB-11', 'dept' => 'LAB', 'type' => 'PC Desktop'],
        ];

        foreach ($kantorDevices as $d) {
            $dept = $departmentsMap[$d['dept']] ?? null;
            ComputerDevice::updateOrCreate(
                ['comp_name' => $d['comp'], 'location' => 'kantor'],
                [
                    'user_name' => $d['user'],
                    'department_id' => $dept ? $dept->id : null,
                    'device_type' => $d['type'],
                    'status' => 'active',
                ]
            );
        }

        // Data Unit Pabrik (PT. Aneka Coffee Industry)
        $pabrikDevices = [
            ['user' => 'Abiyyu', 'comp' => 'PPIC-02', 'dept' => 'PPIC', 'type' => 'PC Desktop'],
            ['user' => 'Nunuk', 'comp' => 'PPIC-03', 'dept' => 'PPIC', 'type' => 'PC Desktop'],
            ['user' => 'Arifin', 'comp' => 'PPIC-04', 'dept' => 'PPIC', 'type' => 'PC Desktop'],
        ];

        foreach ($pabrikDevices as $d) {
            $dept = $departmentsMap[$d['dept']] ?? null;
            ComputerDevice::updateOrCreate(
                ['comp_name' => $d['comp'], 'location' => 'pabrik'],
                [
                    'user_name' => $d['user'],
                    'department_id' => $dept ? $dept->id : null,
                    'device_type' => $d['type'],
                    'status' => 'active',
                ]
            );
        }
    }
}
