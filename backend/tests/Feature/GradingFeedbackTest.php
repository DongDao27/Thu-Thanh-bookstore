<?php

namespace Tests\Feature;

use App\Models\OperationalAlert;
use App\Models\PaymentAttempt;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Notifications\OperationalAlertNotification;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\Support\OrderFixture;
use Tests\TestCase;

class GradingFeedbackTest extends TestCase
{
    use OrderFixture, RefreshDatabase;

    public function test_unrelated_order_and_receipt_updates_keep_warm_snapshot_but_sales_changes_invalidate(): void
    {
        $product = Product::factory()->create(['is_bestseller' => false]);
        $order = $this->orderFor([$product]);
        $attempt = PaymentAttempt::create([
            'order_id' => $order->id, 'provider' => 'vnpay',
            'provider_reference' => 'grading-feedback-attempt',
            'requested_amount' => $order->payable_total, 'status' => 'succeeded',
            'expires_at' => now()->addMinutes(5),
        ]);
        $receipt = PaymentReceipt::create([
            'attempt_id' => $attempt->id,
            'provider' => 'cash', 'provider_transaction_id' => 'grading-feedback-receipt',
            'order_id' => $order->id, 'received_amount' => $order->payable_total,
            'provider_paid_at' => now(), 'verified_at' => now(), 'allocation' => 'accepted',
        ]);
        $this->getJson('/api/v1/storefront')->assertJsonCount(0, 'data.bestseller');
        $revision = Cache::get('storefront:revision');
        $order->update(['recipient_name' => 'Updated recipient', 'version' => 2]);
        $receipt->refresh()->update(['verified_at' => now()->addMinute()]);
        $this->assertSame($revision, Cache::get('storefront:revision'));
        $order->update(['payment_status' => 'paid']);
        $this->assertNotSame($revision, Cache::get('storefront:revision'));
        $this->getJson('/api/v1/storefront')->assertJsonPath('data.bestseller.0.sold_count', 1);
        $receipt->update(['allocation' => 'duplicate']);
        $this->getJson('/api/v1/storefront')->assertJsonCount(0, 'data.bestseller');
    }

    public function test_snapshot_caches_forty_newest_public_products_without_extra_queries_on_hit(): void
    {
        Product::factory()->count(45)->create();
        Product::factory()->create(['is_active' => false]);
        $first = $this->getJson('/api/v1/storefront')->assertOk()
            ->assertJsonCount(40, 'data.catalog')->assertJsonCount(12, 'data.new');
        DB::enableQueryLog();
        DB::flushQueryLog();
        $second = $this->getJson('/api/v1/storefront')->assertOk();
        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertSame($first->json('data.catalog'), $second->json('data.catalog'));
    }

    public function test_ipn_failure_keeps_provider_ack_and_notifies_operator_without_sensitive_context(): void
    {
        Notification::fake();
        config(['operations.enabled' => true, 'operations.alert_email' => 'operator@example.test']);
        $this->mock(PaymentGateway::class, function ($mock) {
            $mock->shouldReceive('verifyCallback')->twice()->andThrow(new \RuntimeException('private provider payload'));
        });
        $this->getJson('/api/v1/payments/vnpay/ipn?vnp_SecureHash=private-signature')->assertExactJson(['RspCode' => '99', 'Message' => 'Unknown error']);
        $this->getJson('/api/v1/payments/vnpay/ipn')->assertJsonPath('RspCode', '99');
        $alert = OperationalAlert::where('code', 'vnpay_ipn_failure')->sole();
        $this->assertSame(['exception_class' => \RuntimeException::class], $alert->context);
        Notification::assertSentOnDemandTimes(OperationalAlertNotification::class, 1);
        $this->assertStringNotContainsString('private', json_encode($alert->context));
    }
}
