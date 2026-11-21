<?php

namespace App\Providers;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Mail\Transport\ResendTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Resend\Client;
use Resend\Transporters\HttpTransporter;
use Resend\ValueObjects\ApiKey;
use Resend\ValueObjects\Transporter\BaseUri;
use Resend\ValueObjects\Transporter\Headers;

class ResendServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, fn () => new Client(new HttpTransporter(
            new HttpClient(['connect_timeout' => 3, 'timeout' => 10]),
            BaseUri::from('api.resend.com'),
            Headers::withAuthorization(ApiKey::from((string) config('services.resend.key'))),
        )));
    }

    public function boot(): void
    {
        // Bound HTTP deadlines keep a provider outage from exhausting queue workers.
        Mail::extend('resend', fn () => new ResendTransport($this->app->make(Client::class)));
    }
}
