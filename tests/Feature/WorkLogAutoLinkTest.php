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
        $device = ComputerDevice::where('comp_name', 'SC')->where('user_name', 'LIKE', '%Gbaku%')->first();
        $this->assertNotNull($device);

        $currentPeriod = date('Y-m');
        $record = PcMaintenanceRecord::where('computer_device_id', $device->id)
            ->where('period', $currentPeriod)
            ->first();

        $this->assertNotNull($record, 'PC Maintenance Record should be auto-created for the period');
        $this->assertEquals($user->id, $record->technician_id);
    }
}
