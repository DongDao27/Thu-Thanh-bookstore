<?php

use App\Http\Middleware\RequireAdminSession;
use App\Support\ApiSecurityDocumentation;

return [
    'api_path' => 'api/v1',
    'export_path' => storage_path('logs/openapi.generated.json'),
    'info' => ['version' => '1.0.0', 'description' => 'Thu Thanh API generated from Laravel routes, controllers and validation. Browser endpoints use session cookies and CSRF; integrations require scoped Sanctum PATs.'],
    'middleware' => ['web', RequireAdminSession::class],
    'dev_tools' => ['enabled' => false],
    'security_strategy' => ApiSecurityDocumentation::class,
];
