<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\NewsletterSubscription;
use App\Notifications\NewsletterSubscribed;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NewsletterController extends Controller
{
    public function __invoke(SubscribeNewsletterRequest $request): JsonResponse
    {
        // Submitting the subscription form records the intent to subscribe (D110).
        // A unique key also makes concurrent submissions converge on one row.
        DB::transaction(function () use ($request): void {
            $subscription = NewsletterSubscription::firstOrCreate(
                ['email' => $request->validated('email')],
                ['consented_at' => now()],
            );
            // Only the request which inserted the unique email queues confirmation.
            // The encrypted notification waits for commit, so a rollback sends nothing.
            if ($subscription->wasRecentlyCreated) {
                Notification::route('mail', $subscription->email)->notify(new NewsletterSubscribed);
            }
        });

        return ApiResponse::data(['message' => 'Đã ghi nhận đăng ký nhận bản tin.'], 202);
    }
}
