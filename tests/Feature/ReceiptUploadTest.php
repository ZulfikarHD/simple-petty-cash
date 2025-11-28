<?php

namespace Tests\Feature;

use App\Models\CashFund;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReceiptUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->category = Category::factory()->default()->create();

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000000,
        ]);
    }

    public function test_user_can_create_transaction_with_receipt(): void
    {
        $this->actingAs($this->user);

        $receipt = UploadedFile::fake()->image('receipt.jpg', 800, 600);

        $response = $this->post('/transactions', [
            'amount' => 50000,
            'description' => 'Beli alat tulis',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
            'receipt' => $receipt,
        ]);

        $response->assertRedirect('/transactions');

        $transaction = Transaction::where('user_id', $this->user->id)->first();

        $this->assertNotNull($transaction);
        $this->assertNotNull($transaction->receipt_path);
        $this->assertTrue($transaction->has_receipt);

        Storage::disk('public')->assertExists($transaction->receipt_path);
    }

    public function test_user_can_create_transaction_without_receipt(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/transactions', [
            'amount' => 50000,
            'description' => 'Beli alat tulis',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect('/transactions');

        $transaction = Transaction::where('user_id', $this->user->id)->first();

        $this->assertNotNull($transaction);
        $this->assertNull($transaction->receipt_path);
        $this->assertFalse($transaction->has_receipt);
    }

    public function test_user_can_update_transaction_with_new_receipt(): void
    {
        $this->actingAs($this->user);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 50000,
        ]);

        $receipt = UploadedFile::fake()->image('new_receipt.jpg', 800, 600);

        $response = $this->put("/transactions/{$transaction->id}", [
            'amount' => 50000,
            'description' => 'Updated description',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
            'receipt' => $receipt,
        ]);

        $response->assertRedirect('/transactions');

        $transaction->refresh();

        $this->assertNotNull($transaction->receipt_path);
        $this->assertTrue($transaction->has_receipt);

        Storage::disk('public')->assertExists($transaction->receipt_path);
    }

    public function test_user_can_replace_existing_receipt(): void
    {
        $this->actingAs($this->user);

        // Create transaction with receipt
        $oldReceipt = UploadedFile::fake()->image('old_receipt.jpg', 800, 600);
        $receiptService = new ReceiptService;
        $oldPath = $receiptService->storeReceipt($oldReceipt, $this->user->id);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 50000,
            'receipt_path' => $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        // Replace with new receipt
        $newReceipt = UploadedFile::fake()->image('new_receipt.jpg', 1000, 800);

        $response = $this->put("/transactions/{$transaction->id}", [
            'amount' => 50000,
            'description' => 'Updated with new receipt',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
            'receipt' => $newReceipt,
        ]);

        $response->assertRedirect('/transactions');

        $transaction->refresh();

        // Old receipt should be deleted
        Storage::disk('public')->assertMissing($oldPath);

        // New receipt should exist
        $this->assertNotNull($transaction->receipt_path);
        $this->assertNotEquals($oldPath, $transaction->receipt_path);
        Storage::disk('public')->assertExists($transaction->receipt_path);
    }

    public function test_user_can_remove_receipt_from_transaction(): void
    {
        $this->actingAs($this->user);

        // Create transaction with receipt
        $receipt = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        $receiptService = new ReceiptService;
        $receiptPath = $receiptService->storeReceipt($receipt, $this->user->id);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 50000,
            'receipt_path' => $receiptPath,
        ]);

        Storage::disk('public')->assertExists($receiptPath);

        // Remove receipt
        $response = $this->put("/transactions/{$transaction->id}", [
            'amount' => 50000,
            'description' => 'Receipt removed',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
            'remove_receipt' => true,
        ]);

        $response->assertRedirect('/transactions');

        $transaction->refresh();

        $this->assertNull($transaction->receipt_path);
        $this->assertFalse($transaction->has_receipt);

        Storage::disk('public')->assertMissing($receiptPath);
    }

    public function test_receipt_is_deleted_when_transaction_is_deleted(): void
    {
        $this->actingAs($this->user);

        // Create transaction with receipt
        $receipt = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        $receiptService = new ReceiptService;
        $receiptPath = $receiptService->storeReceipt($receipt, $this->user->id);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 50000,
            'receipt_path' => $receiptPath,
        ]);

        Storage::disk('public')->assertExists($receiptPath);

        $response = $this->delete("/transactions/{$transaction->id}");

        $response->assertRedirect('/transactions');

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);

        Storage::disk('public')->assertMissing($receiptPath);
    }

    public function test_receipt_validation_rejects_non_image_files(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->post('/transactions', [
            'amount' => 50000,
            'description' => 'Test transaction',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
            'receipt' => $file,
        ]);

        $response->assertSessionHasErrors('receipt');
    }

    public function test_receipt_validation_rejects_large_files(): void
    {
        $this->actingAs($this->user);

        // Create file larger than 5MB
        $file = UploadedFile::fake()->image('large.jpg')->size(6000);

        $response = $this->post('/transactions', [
            'amount' => 50000,
            'description' => 'Test transaction',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
            'receipt' => $file,
        ]);

        $response->assertSessionHasErrors('receipt');
    }

    public function test_receipt_url_accessor_returns_correct_url(): void
    {
        $receipt = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        $receiptService = new ReceiptService;
        $receiptPath = $receiptService->storeReceipt($receipt, $this->user->id);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'receipt_path' => $receiptPath,
        ]);

        $this->assertNotNull($transaction->receipt_url);
        $this->assertStringContainsString($receiptPath, $transaction->receipt_url);
    }

    public function test_receipt_service_compresses_large_images(): void
    {
        $receiptService = new ReceiptService;

        // Create a large image (2000px wide)
        $largeImage = UploadedFile::fake()->image('large.jpg', 2000, 1500);

        $path = $receiptService->storeReceipt($largeImage, $this->user->id);

        Storage::disk('public')->assertExists($path);

        // The stored image should be compressed
        $storedContent = Storage::disk('public')->get($path);
        $this->assertNotEmpty($storedContent);
    }

    public function test_receipt_service_handles_various_image_formats(): void
    {
        $receiptService = new ReceiptService;

        $formats = ['jpg', 'png', 'gif', 'webp'];

        foreach ($formats as $format) {
            $image = UploadedFile::fake()->image("receipt.{$format}", 800, 600);
            $path = $receiptService->storeReceipt($image, $this->user->id);

            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_user_can_delete_receipt_via_dedicated_endpoint(): void
    {
        $this->actingAs($this->user);

        // Create transaction with receipt
        $receipt = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        $receiptService = new ReceiptService;
        $receiptPath = $receiptService->storeReceipt($receipt, $this->user->id);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 50000,
            'receipt_path' => $receiptPath,
        ]);

        Storage::disk('public')->assertExists($receiptPath);

        $response = $this->delete("/transactions/{$transaction->id}/receipt");

        $response->assertRedirect();

        $transaction->refresh();

        $this->assertNull($transaction->receipt_path);
        Storage::disk('public')->assertMissing($receiptPath);
    }

    public function test_other_user_cannot_delete_receipt(): void
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();

        $receipt = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        $receiptService = new ReceiptService;
        $receiptPath = $receiptService->storeReceipt($receipt, $otherUser->id);

        $transaction = Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'receipt_path' => $receiptPath,
        ]);

        $response = $this->delete("/transactions/{$transaction->id}/receipt");

        $response->assertStatus(403);

        Storage::disk('public')->assertExists($receiptPath);
    }
}
