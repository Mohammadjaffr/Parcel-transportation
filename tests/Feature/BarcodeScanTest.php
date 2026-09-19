<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Models\Branch;
use App\Models\App;
use Illuminate\Support\Facades\DB;

class BarcodeScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_three_shipments_from_same_package_consecutively(): void
    {
        // 1. Setup Data
        $app = App::create([
            'name' => 'Test App',
            'phone' => '123456789',
        ]);

        $senderBranch = Branch::create([
            'app_id' => $app->id,
            'name' => 'Sender Branch',
            'phone' => '111111',
            'address' => 'Test Address',
            'city' => 'Test City',
        ]);
        $receiverBranch = Branch::create([
            'app_id' => $app->id,
            'name' => 'Receiver Branch',
            'phone' => '222222',
            'address' => 'Test Address',
            'city' => 'Test City',
        ]);
        
        $user = User::create([
            'name' => 'Test User',
            'phone' => '0501234567',
            'password' => bcrypt('password'),
            'app_id' => $app->id,
            'branch_id' => $receiverBranch->id,
            'type' => 'admin',
            'status' => 'active',
        ]);
        
        // Create Package
        $package = ShipmentPackage::create([
            'app_id' => $app->id,
            'sender_branch_id' => $senderBranch->id,
            'receiver_branch_id' => $receiverBranch->id,
            'status' => 'in_transit',
            'tracking_number' => 'PKG-123456',
            'created_by' => $user->id,
        ]);

        // Create 3 shipments assigned to the same package
        $shipments = [];
        for ($i = 1; $i <= 3; $i++) {
            $shipments[] = Shipment::create([
                'app_id' => $app->id,
                'sender_branch_id' => $senderBranch->id,
                'receiver_branch_id' => $receiverBranch->id,
                'shipment_package_id' => $package->id,
                'status' => 'in_transit',
                'amount_to_collect_from_receiver' => 100,
                'tracking_number' => 'TRK-' . $i,
                'weight' => 1,
                'created_by' => $user->id,
            ]);
        }

        $this->withoutMiddleware();
        $this->actingAs($user);

        // 2. Scan first shipment
        $response1 = $this->postJson(route('shipment.incoming.scan.receive'), [
            'bond_number' => $shipments[0]->bond_number
        ]);
        
        $response1->assertStatus(200);
        $response1->assertJsonPath('success', true);
        
        // Assert shipment is received
        $this->assertDatabaseHas('shipments', [
            'id' => $shipments[0]->id,
            'status' => 'received_at_branch'
        ]);
        
        // Assert package is still in transit (has 2 pending)
        $this->assertDatabaseHas('shipment_packages', [
            'id' => $package->id,
            'status' => 'in_transit'
        ]);

        // 3. Scan second shipment
        $response2 = $this->postJson(route('shipment.incoming.scan.receive'), [
            'bond_number' => $shipments[1]->bond_number
        ]);
        $response2->assertStatus(200);

        // 4. Scan third shipment (last one)
        $response3 = $this->postJson(route('shipment.incoming.scan.receive'), [
            'bond_number' => $shipments[2]->bond_number
        ]);
        $response3->assertStatus(200);

        // Assert all shipments received
        foreach ($shipments as $shipment) {
            $this->assertDatabaseHas('shipments', [
                'id' => $shipment->id,
                'status' => 'received_at_branch'
            ]);
        }

        // Assert package is NOW closed (delivered) since all items are received
        $this->assertDatabaseHas('shipment_packages', [
            'id' => $package->id,
            'status' => 'delivered'
        ]);
    }
}
