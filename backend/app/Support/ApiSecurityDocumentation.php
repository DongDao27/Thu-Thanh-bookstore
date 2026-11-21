<?php

namespace App\Support;

use Dedoc\Scramble\Configuration\SecurityDocumentationContext;
use Dedoc\Scramble\Contracts\SecurityDocumentationStrategy;
use Dedoc\Scramble\GeneratorConfig;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\SecurityRequirement;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\RouteInfo;

class ApiSecurityDocumentation implements SecurityDocumentationStrategy
{
    public function configure(SecurityDocumentationContext $context): GeneratorConfig
    {
        return $context->config->withDocumentTransformers(function (OpenApi $api): void {
            $api->components->addSecurityScheme('bearerToken', SecurityScheme::http('bearer'));
            $api->components->addSecurityScheme('sessionCookie', SecurityScheme::apiKey('cookie', config('session.cookie')));
            $api->components->addSecurityScheme('xsrf', SecurityScheme::apiKey('header', 'X-XSRF-TOKEN'));
        })->withOperationTransformers(function (Operation $operation, RouteInfo $route): void {
            $middleware = $route->route->gatherMiddleware();
            $requirements = [];
            if (in_array('personal-token', $middleware, true)) {
                $requirements['bearerToken'] = [];
            } else {
                if (in_array('auth:sanctum', $middleware, true)) {
                    $requirements['sessionCookie'] = [];
                }
                if (in_array('web', $middleware, true) && ! in_array(strtoupper($operation->method), ['GET', 'HEAD'], true)) {
                    $requirements['xsrf'] = [];
                }
            }
            $operation->security = $requirements === [] ? [] : [new SecurityRequirement($requirements)];
        });
    }
}
