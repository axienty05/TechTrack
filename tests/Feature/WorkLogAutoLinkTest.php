<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ComputerDevice;
use App\Models\Department;
use App\Models\Category;
use App\Models\PcMaintenanceRecord;
use App\Livewire\WorkLogs\Index as WorkLogsIndex;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkLogAutoLinkTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $pemakai1 = \App\Models\Pemakai::firstOrCreate(
            ['nama' => 'Gbaku (PC Lama)'],
            ['comp_name' => 'SC', 'status' => true]
        );
        ComputerDevice::firstOrCreate(
            ['comp_name' => 'SC'],
            [
                'm_pemakai_id' => $pemakai1->id,
                'location' => 'pabrik',
                'device_type' => 'desktop',
                'status' => 'active',
            ]
        );

        $pemakai2 = \App\Models\Pemakai::firstOrCreate(
            ['nama' => 'GBaku'],
            ['comp_name' => 'RMWH', 'status' => true]
        );
        ComputerDevice::firstOrCreate(
            ['comp_name' => 'RMWH'],
            [
                'm_pemakai_id' => $pemakai2->id,
                'location' => 'pabrik',
                'device_type' => 'desktop',
                'status' => 'active',
            ]
        );
    }

    public function test_find_matching_device_for_maintenance_pc_lama_gbaku()
    {
        $device = WorkLogsIndex::findMatchingComputerDevice('Maintenance PC Lama Gbaku');

        $this->assertNotNull($device, 'Device should be found');
        $this->assertEquals('SC', $device->comp_name);
        $this->assertEquals('Gbaku (PC Lama)', $device->user_name);
        $this->assertEquals('pabrik', $device->location);
    }

    public function test_find_matching_device_for_maintenance_pc_baru_gbaku()
    {
        $device = WorkLogsIndex::findMatchingComputerDevice('Maintenance PC Baru Gbaku');

        $this->assertNotNull($device, 'Device should be found');
        $this->assertEquals('RMWH', $device->comp_name);
        $this->assertEquals('GBaku', $device->user_name);
    }

    public function test_livewire_auto_detection_and_save()
    {
        $user = User::first() ?? User::factory()->create();

        Livewire::actingAs($user)
            ->test(WorkLogsIndex::class)
            ->call('openCreateModal')
            ->set('title', 'Maintenance PC Lama Gbaku')
            ->assertSet('task_type', 'preventive')
            ->assertSet('device_identifier', 'SC')
            ->assertSet('requester_name', 'Gbaku (PC Lama)')
            ->assertSet('detectedDevSummary', 'SC — Gbaku (PC Lama)')
            ->assertSet('detectedDevLocation', 'pabrik')
            ->call('save');

        // Verify that PcMaintenanceRecord is created for device 49
        $device = ComputerDevice::where('comp_name', 'SC')->whereHas('pemakai', fn($q) => $q->where('nama', 'LIKE', '%Gbaku%'))->first();
        $this->assertNotNull($device);

        $currentPeriod = date('Y-m');
        $record = PcMaintenanceRecord::where('computer_device_id', $device->id)
            ->where('period', $currentPeriod)
            ->first();

        $this->assertNotNull($record, 'PC Maintenance Record should be auto-created for the period');
        $this->assertEquals($user->id, $record->technician_id);
    }

    public function test_non_maintenance_task_type_preservation()
    {
        $user = User::first() ?? User::factory()->create();
        $cat = Category::first();

        $uniqueTitle = 'Unit Test Setup Akun Email Lusi ' . uniqid();

        Livewire::actingAs($user)
            ->test(WorkLogsIndex::class)
            ->call('openCreateModal')
            ->set('title', $uniqueTitle)
            ->set('requester_name', 'Lusi')
            ->set('category_id', $cat->id)
            ->set('task_type', 'administrative')
            ->assertSet('task_type', 'administrative')
            ->call('save');

        $log = \App\Models\WorkLog::where('title', $uniqueTitle)->first();
        $this->assertNotNull($log);
        $this->assertEquals('administrative', $log->task_type, 'Task type must remain administrative');

        $record = PcMaintenanceRecord::where('work_log_id', $log->id)->first();
        $this->assertNull($record, 'No PC Maintenance record should be created for non-preventive task');
    }
}
