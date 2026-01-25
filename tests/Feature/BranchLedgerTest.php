<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\Transaction;
use App\Models\BranchLedger;
use App\Models\CustomerPayment;
use App\Models\TransactionCategory;
use App\Services\ShipmentPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BranchLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branchA;
    protected Branch $branchB;
    protected Customer $customer;
    protected User $user;
    protected ShipmentPaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create branches
        $this->branchA = Branch::create([
            'code' => 'BRA',
            'name' => 'Branch A (Sender)',
            'city' => 'City A',
            'address' => 'Address A',
        ]);

        $this->branchB = Branch::create([
            'code' => 'BRB',
            'name' => 'Branch B (Receiver)',
            'city' => 'City B',
            'address' => 'Address B',
        ]);

        // Create customer
        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'phone' => '967700000001',
            'branch_code' => 'BRA',
        ]);

        // Create user for auth
        $this->user = User::factory()->create([
            'branch_code' => 'BRA',
        ]);

        // Create required transaction category
        TransactionCategory::create([
            'name' => 'تحصيل شحنات',
            'type' => 'in',
            'code' => 'SHIPMENT_PAYMENT',
            'is_active' => true,
        ]);

        $this->service = new ShipmentPaymentService();
    }

    /**
     * Helper to create a shipment
     */
    protected function createShipment(string $paymentMethod, float $totalAmount, string $status = 'pending'): Shipment
    {
        return Shipment::create([
            'sender_branch_code' => $this->branchA->code,
            'created_branch_code' => $this->branchA->code,
            'receiver_branch_code' => $this->branchB->code,
            'sender_customer_id' => $this->customer->id,
            'receiver_customer_id' => $this->customer->id,
            'total_amount' => $totalAmount,
            'payment_method' => $paymentMethod,
            'status' => $status,
            'package_type' => 'parcel',
        ]);
    }

    /**
     * Test A: Full COD Delivery
     * 
     * When a COD shipment (1000 SAR) is delivered:
     * - Transaction table has +1000 for Branch B
     * - BranchLedger has Debit 1000 for Branch B
     * - BranchLedger has Credit 1000 for Branch A
     */
    public function test_full_cod_delivery_creates_transaction_and_ledger_entries(): void
    {
        // Arrange
        $shipment = $this->createShipment('cod', 1000.00, 'delivered');

        // Act
        $this->service->createCodBranchTransactionOnDelivery($shipment);

        // Assert: Transaction created for receiver branch
        $this->assertDatabaseHas('transactions', [
            'branch_code' => 'BRB',
            'shipment_id' => $shipment->id,
            'amount' => 1000.00,
        ]);

        // Assert: Receiver branch has DEBIT entry (owes money)
        $this->assertDatabaseHas('branch_ledgers', [
            'branch_code' => 'BRB',
            'related_branch_code' => 'BRA',
            'shipment_id' => $shipment->id,
            'type' => 'shipment_cod',
            'debit' => 1000.00,
            'credit' => 0.00,
        ]);

        // Assert: Sender branch has CREDIT entry (is owed money)
        $this->assertDatabaseHas('branch_ledgers', [
            'branch_code' => 'BRA',
            'related_branch_code' => 'BRB',
            'shipment_id' => $shipment->id,
            'type' => 'shipment_cod',
            'debit' => 0.00,
            'credit' => 1000.00,
        ]);
    }

    /**
     * Test B: Partial Payment Delivery
     * 
     * When a partial payment shipment (1000 SAR, 200 prepaid) is delivered:
     * - Only 800 should be recorded (not 1000)
     */
    public function test_partial_payment_delivery_only_records_remaining_amount(): void
    {
        // Arrange
        $shipment = $this->createShipment('partial_payment', 1000.00, 'delivered');

        // Simulate prepaid amount of 200
        CustomerPayment::create([
            'shipment_id' => $shipment->id,
            'customer_id' => $this->customer->id,
            'branch_code' => 'BRA',
            'amount' => 200.00,
            'payment_method' => 'cash',
            'payment_date' => now(),
        ]);

        // Act
        $this->service->createCodBranchTransactionOnDelivery($shipment);

        // Assert: Transaction shows 800, not 1000
        $this->assertDatabaseHas('transactions', [
            'branch_code' => 'BRB',
            'shipment_id' => $shipment->id,
            'amount' => 800.00,
        ]);

        // Assert: Ledger entries reflect 800
        $this->assertDatabaseHas('branch_ledgers', [
            'branch_code' => 'BRB',
            'shipment_id' => $shipment->id,
            'debit' => 800.00,
        ]);

        $this->assertDatabaseHas('branch_ledgers', [
            'branch_code' => 'BRA',
            'shipment_id' => $shipment->id,
            'credit' => 800.00,
        ]);
    }

    /**
     * Test C: Prepaid Shipment (No Debt)
     * 
     * When a fully prepaid shipment is delivered:
     * - No ledger records should be created
     */
    public function test_prepaid_shipment_creates_no_ledger_entries(): void
    {
        // Arrange
        $shipment = $this->createShipment('prepaid', 1000.00, 'delivered');

        // Act
        $this->service->createCodBranchTransactionOnDelivery($shipment);

        // Assert: No ledger entries created
        $this->assertDatabaseMissing('branch_ledgers', [
            'shipment_id' => $shipment->id,
        ]);
    }

    /**
     * Test D: Idempotency (Prevent Duplicates)
     * 
     * Calling the service twice should only create one set of records
     */
    public function test_calling_service_twice_creates_only_one_set_of_records(): void
    {
        // Arrange
        $shipment = $this->createShipment('cod', 1000.00, 'delivered');

        // Act: Call twice
        $this->service->createCodBranchTransactionOnDelivery($shipment);
        $this->service->createCodBranchTransactionOnDelivery($shipment);

        // Assert: Only one transaction
        $transactionCount = Transaction::where('shipment_id', $shipment->id)->count();
        $this->assertEquals(1, $transactionCount, 'Should have exactly 1 transaction');

        // Assert: Only two ledger entries (one per branch)
        $ledgerCount = BranchLedger::where('shipment_id', $shipment->id)->count();
        $this->assertEquals(2, $ledgerCount, 'Should have exactly 2 ledger entries');
    }

    /**
     * Test E: Non-delivered shipment creates no records
     */
    public function test_non_delivered_shipment_creates_no_records(): void
    {
        // Arrange: Shipment is still pending
        $shipment = $this->createShipment('cod', 1000.00, 'pending');

        // Act
        $this->service->createCodBranchTransactionOnDelivery($shipment);

        // Assert: No records created
        $this->assertDatabaseMissing('branch_ledgers', [
            'shipment_id' => $shipment->id,
        ]);

        $this->assertDatabaseMissing('transactions', [
            'shipment_id' => $shipment->id,
        ]);
    }
}
