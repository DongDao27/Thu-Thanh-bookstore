<?php

namespace Tests\Feature;

use App\Events\OrderChanged;
use App\Models\Product;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\Support\OrderFixture;
use Tests\TestCase;

class RealtimeTest extends TestCase
{
    use DatabaseMigrations, OrderFixture;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.app_id' => 'fixture-app',
            'broadcasting.connections.pusher.key' => 'fixture-key',
            'broadcasting.connections.pusher.secret' => 'fixture-secret',
        ]);
        require base_path('routes/channels.php');
        Queue::fake();
    }

    public function test_private_channel_requires_session_and_owner_or_admin(): void
    {
        $order = $this->orderFor([Product::factory()->create()]);
        $body = ['socket_id' => '123.456', 'channel_name' => 'private-orders.'.$order->order_code];
        $this->postJson('/api/v1/broadcasting/auth', $body)->assertUnauthorized();
        $this->actingAs(User::factory()->create(), 'web')->postJson('/api/v1/broadcasting/auth', $body)->assertForbidden();
        $this->actingAs($order->user, 'web')->postJson('/api/v1/broadcasting/auth', $body)->assertOk()->assertJsonStructure(['data' => ['auth']])->assertDontSee('fixture-secret');
        $this->actingAs(User::factory()->create(['role' => 'admin']), 'web')->postJson('/api/v1/broadcasting/auth', $body)->assertOk();
        $this->postJson('/api/v1/broadcasting/auth', [...$body, 'channel_name' => 'private-orders.UNKNOWN'])->assertForbidden();
        $this->postJson('/api/v1/broadcasting/auth', [...$body, 'channel_name' => 'public-orders.all'])->assertUnprocessable();
        $this->withToken('fixture-bearer')->postJson('/api/v1/broadcasting/auth', $body)->assertForbidden();
    }

    public function test_event_is_queued_only_after_commit_and_rollback_publishes_nothing(): void
    {
        $order = $this->orderFor([Product::factory()->create()]);
        DB::beginTransaction();
        $order->update(['fulfillment_status' => 'preparing', 'version' => $order->version + 1]);
        Queue::assertNothingPushed();
        DB::rollBack();
        Queue::assertNothingPushed();

        DB::beginTransaction();
        $order = $order->fresh();
        $order->update(['fulfillment_status' => 'preparing', 'version' => $order->version + 1]);
        Queue::assertNothingPushed();
        DB::commit();
        Queue::assertPushed(BroadcastEvent::class, function ($job) use ($order) {
            $this->assertSame(['order_code' => $order->order_code, 'version' => $order->version], $job->event->broadcastWith());
            $this->assertSame('private-orders.'.$order->order_code, $job->event->broadcastOn()[0]->name);

            return $job->event instanceof OrderChanged;
        });
    }
}
